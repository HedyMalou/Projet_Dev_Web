<?php
session_start();
require_once '../backend/connexion.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['tuteur', 'jury'])) {
    header('Location: login.php');
    exit;
}

$id_utilisateur = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Infos utilisateur
$stmt = $pdo->prepare("SELECT nom, prenom FROM UTILISATEUR WHERE id = ?");
$stmt->execute([$id_utilisateur]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si tuteur : récupérer ses étudiants suivis
$etudiants = [];
if ($role === 'tuteur') {
    $stmt = $pdo->prepare("SELECT t.id as id_tuteur FROM TUTEUR t WHERE t.id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    $tuteur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tuteur) {
        $stmt = $pdo->prepare("
            SELECT u.nom, u.prenom, e.filiere, e.promotion, c.statut, c.id as id_candidature, o.titre as offre
            FROM SUIVI s
            JOIN CANDIDATURE c ON c.id = s.id_candidature
            JOIN ETUDIANT e ON e.id = c.id_etudiant
            JOIN UTILISATEUR u ON u.id = e.id_utilisateur
            JOIN OFFRE_STAGE o ON o.id = c.id_offre
            WHERE s.id_tuteur = ?
        ");
        $stmt->execute([$tuteur['id_tuteur']]);
        $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Si jury : récupérer toutes les candidatures à évaluer
$candidatures_jury = [];
if ($role === 'jury') {
    $stmt = $pdo->prepare("
        SELECT c.id, c.statut, u.nom, u.prenom, e.filiere, o.titre as offre, ent.nom_entreprise
        FROM CANDIDATURE c
        JOIN ETUDIANT e ON e.id = c.id_etudiant
        JOIN UTILISATEUR u ON u.id = e.id_utilisateur
        JOIN OFFRE_STAGE o ON o.id = c.id_offre
        JOIN ENTREPRISE ent ON ent.id = o.id_entreprise
        WHERE c.statut = 'validee'
        ORDER BY c.date_candidature DESC
    ");
    $stmt->execute();
    $candidatures_jury = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$nb_etudiants = count($etudiants);
$nb_jury = count($candidatures_jury);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard <?= $role === 'tuteur' ? 'Tuteur' : 'Jury' ?> — Plateforme Stages</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <style>
    :root {
      --bleu: #1a3a5c;
      --bleu-clair: #2d6a9f;
      --bordure: #d1dce8;
      --texte: #1c1c1c;
      --gris: #6b7280;
      --bg: #f0f4f8;
    }

    body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--texte); }

    .sidebar {
      width: 240px; min-height: 100vh; background: var(--bleu);
      position: fixed; top: 0; left: 0;
      display: flex; flex-direction: column; padding: 24px 0; z-index: 100;
    }

    .sidebar-logo {
      display: flex; align-items: center; gap: 10px;
      padding: 0 24px 28px;
      border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 16px;
    }

    .logo-carre {
      width: 34px; height: 34px; background: white; border-radius: 7px;
      display: flex; align-items: center; justify-content: center;
      font-family: 'DM Serif Display', serif; font-size: 15px; color: var(--bleu); font-weight: 700;
    }

    .logo-texte { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.9); }

    .nav-item {
      display: flex; align-items: center; gap: 12px; padding: 11px 24px;
      color: rgba(255,255,255,0.65); text-decoration: none; font-size: 14px;
      font-weight: 500; transition: all 0.15s; border-left: 3px solid transparent;
    }

    .nav-item:hover { background: rgba(255,255,255,0.07); color: white; }
    .nav-item.active { background: rgba(255,255,255,0.1); color: white; border-left-color: white; }
    .nav-icon { width: 18px; height: 18px; }

    .sidebar-footer {
      margin-top: auto; padding: 16px 24px;
      border-top: 1px solid rgba(255,255,255,0.1);
    }

    .user-info { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }

    .avatar {
      width: 34px; height: 34px; border-radius: 50%;
      background: rgba(255,255,255,0.15);
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; font-weight: 600; color: white;
    }

    .user-nom { font-size: 13px; font-weight: 600; color: white; }
    .user-role { font-size: 11px; color: rgba(255,255,255,0.5); }

    .btn-deconnexion {
      width: 100%; padding: 8px; background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.15); border-radius: 6px;
      color: rgba(255,255,255,0.7); font-size: 13px; cursor: pointer;
      text-align: center; text-decoration: none; display: block;
      font-family: 'DM Sans', sans-serif;
    }

    .btn-deconnexion:hover { background: rgba(255,255,255,0.14); color: white; }

    .main { margin-left: 240px; padding: 32px; min-height: 100vh; }

    .page-header { margin-bottom: 28px; }
    .page-header h1 { font-family: 'DM Serif Display', serif; font-size: 26px; margin-bottom: 4px; }
    .page-header p { font-size: 14px; color: var(--gris); }

    .kpi-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 28px; }

    .kpi-card { background: white; border-radius: 12px; padding: 20px 24px; border: 0.5px solid var(--bordure); }
    .kpi-label { font-size: 12px; color: var(--gris); font-weight: 500; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
    .kpi-valeur { font-size: 28px; font-weight: 600; color: var(--texte); margin-bottom: 4px; }
    .kpi-sous { font-size: 12px; color: var(--gris); }

    .section-card { background: white; border-radius: 12px; padding: 24px; border: 0.5px solid var(--bordure); margin-bottom: 28px; }
    .section-titre { font-size: 15px; font-weight: 600; color: var(--texte); margin-bottom: 16px; }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead th { padding: 10px 12px; background: var(--bg); color: var(--gris); font-weight: 500; font-size: 12px; text-align: left; border-bottom: 1px solid var(--bordure); }
    tbody td { padding: 12px; border-bottom: 0.5px solid var(--bordure); vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafcff; }

    .badge-attente { font-size: 11px; padding: 3px 10px; border-radius: 20px; background: #fef9c3; color: #a16207; font-weight: 500; }
    .badge-validee { font-size: 11px; padding: 3px 10px; border-radius: 20px; background: #f0fdf4; color: #16a34a; font-weight: 500; }

    .btn-action {
      font-size: 12px; padding: 5px 12px; border-radius: 6px;
      border: 1.5px solid var(--bleu); color: var(--bleu); background: white;
      font-family: 'DM Sans', sans-serif; font-weight: 500; cursor: pointer; text-decoration: none;
    }

    .btn-action:hover { background: var(--bleu); color: white; }

    .form-label-sm { font-size: 12px; font-weight: 500; color: var(--gris); margin-bottom: 5px; display: block; }

    .form-control, .form-select {
      border: 1.5px solid var(--bordure); border-radius: 8px; padding: 9px 12px;
      font-size: 13px; font-family: 'DM Sans', sans-serif; color: var(--texte); background: #fafcff;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--bleu-clair); box-shadow: 0 0 0 3px rgba(45,106,159,0.1); outline: none;
    }

    .btn-valider {
      padding: 9px 20px; background: var(--bleu); color: white; border: none;
      border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px;
      font-weight: 600; cursor: pointer; margin-top: 8px;
    }

    .btn-valider:hover { background: var(--bleu-clair); }

    .vide { text-align: center; padding: 32px; color: var(--gris); font-size: 14px; }

    @media (max-width: 992px) {
      .sidebar { display: none; }
      .main { margin-left: 0; padding: 20px; }
      .kpi-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<nav class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-carre">CY</div>
    <span class="logo-texte">Plateforme Stages</span>
  </div>

  <a href="dashboard_tuteur.php" class="nav-item active">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
      <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
    </svg>
    Tableau de bord
  </a>

  <a href="mes_etudiants.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    Mes étudiants
  </a>

  <a href="documents.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
      <polyline points="14,2 14,8 20,8"/>
    </svg>
    Documents
  </a>

  <div class="sidebar-footer">
    <div class="user-info">
      <div class="avatar">
        <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
      </div>
      <div>
        <div class="user-nom"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></div>
        <div class="user-role"><?= $role === 'tuteur' ? 'Tuteur' : 'Jury' ?></div>
      </div>
    </div>
    <a href="../backend/logout.php" class="btn-deconnexion">Déconnexion</a>
  </div>
</nav>

<main class="main">

  <div class="page-header">
    <h1>Bonjour, <?= htmlspecialchars($user['prenom']) ?> 👋</h1>
    <p><?= $role === 'tuteur' ? 'Suivez vos étudiants et validez leurs conventions.' : 'Consultez les dossiers et évaluez les stages.' ?></p>
  </div>

  <div class="kpi-grid">
    <?php if ($role === 'tuteur'): ?>
      <div class="kpi-card">
        <div class="kpi-label">Étudiants suivis</div>
        <div class="kpi-valeur"><?= $nb_etudiants ?></div>
        <div class="kpi-sous">en cours de stage</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Conventions à valider</div>
        <div class="kpi-valeur">
          <?= count(array_filter($etudiants, fn($e) => $e['statut'] === 'en_attente')) ?>
        </div>
        <div class="kpi-sous">en attente de signature</div>
      </div>
    <?php else: ?>
      <div class="kpi-card">
        <div class="kpi-label">Stages à évaluer</div>
        <div class="kpi-valeur"><?= $nb_jury ?></div>
        <div class="kpi-sous">dossiers validés</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Évaluations terminées</div>
        <div class="kpi-valeur">0</div>
        <div class="kpi-sous">cette année</div>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($role === 'tuteur'): ?>

    <!-- LISTE ÉTUDIANTS SUIVIS -->
    <div class="section-card">
      <div class="section-titre">Mes étudiants suivis</div>
      <?php if (empty($etudiants)): ?>
        <div class="vide">Aucun étudiant assigné pour l'instant.</div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Étudiant</th>
              <th>Filière</th>
              <th>Offre</th>
              <th>Statut</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($etudiants as $e): ?>
              <tr>
                <td><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></td>
                <td><?= htmlspecialchars($e['filiere']) ?></td>
                <td><?= htmlspecialchars($e['offre']) ?></td>
                <td>
                  <?php if ($e['statut'] === 'en_attente'): ?>
                    <span class="badge-attente">En attente</span>
                  <?php else: ?>
                    <span class="badge-validee">Validée</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="dossier_etudiant.php?id=<?= $e['id_candidature'] ?>" class="btn-action">Voir le dossier</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <!-- ZONE COMMENTAIRE -->
    <div class="section-card">
      <div class="section-titre">Ajouter un commentaire de suivi</div>
      <form method="POST" action="../backend/ajouter_commentaire.php">
        <div style="margin-bottom:12px;">
          <label class="form-label-sm">Étudiant concerné</label>
          <select name="id_candidature" class="form-select">
            <?php foreach ($etudiants as $e): ?>
              <option value="<?= $e['id_candidature'] ?>">
                <?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="margin-bottom:12px;">
          <label class="form-label-sm">Commentaire</label>
          <textarea name="contenu" class="form-control" rows="3" placeholder="Remarques sur l'avancement du stage..." required></textarea>
        </div>
        <button type="submit" class="btn-valider">Envoyer le commentaire</button>
      </form>
    </div>

  <?php else: ?>

    <!-- DOSSIERS À ÉVALUER (JURY) -->
    <div class="section-card">
      <div class="section-titre">Dossiers à évaluer</div>
      <?php if (empty($candidatures_jury)): ?>
        <div class="vide">Aucun dossier à évaluer pour l'instant.</div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Étudiant</th>
              <th>Filière</th>
              <th>Entreprise</th>
              <th>Stage</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($candidatures_jury as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                <td><?= htmlspecialchars($c['filiere']) ?></td>
                <td><?= htmlspecialchars($c['nom_entreprise']) ?></td>
                <td><?= htmlspecialchars($c['offre']) ?></td>
                <td>
                  <a href="evaluer.php?id=<?= $c['id'] ?>" class="btn-action">Évaluer</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
