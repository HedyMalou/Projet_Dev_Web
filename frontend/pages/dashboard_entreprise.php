<?php
session_start();
require_once '../backend/connexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header('Location: login.php');
    exit;
}

$id_utilisateur = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT u.nom, u.prenom, e.nom_entreprise, e.secteur, e.id as id_entreprise
    FROM UTILISATEUR u
    JOIN ENTREPRISE e ON e.id_utilisateur = u.id
    WHERE u.id = ?
");
$stmt->execute([$id_utilisateur]);
$entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entreprise) {
    header('Location: login.php');
    exit;
}

// Offres publiées
$stmt = $pdo->prepare("SELECT * FROM OFFRE_STAGE WHERE id_entreprise = ? ORDER BY date_publication DESC");
$stmt->execute([$entreprise['id_entreprise']]);
$offres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Candidatures reçues
$stmt = $pdo->prepare("
    SELECT c.*, u.nom, u.prenom, e.filiere, o.titre as titre_offre
    FROM CANDIDATURE c
    JOIN ETUDIANT e ON e.id = c.id_etudiant
    JOIN UTILISATEUR u ON u.id = e.id_utilisateur
    JOIN OFFRE_STAGE o ON o.id = c.id_offre
    WHERE o.id_entreprise = ?
    ORDER BY c.date_candidature DESC
");
$stmt->execute([$entreprise['id_entreprise']]);
$candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nb_offres = count($offres);
$nb_candidatures = count($candidatures);
$nb_en_attente = count(array_filter($candidatures, fn($c) => $c['statut'] === 'en_attente'));

// Ajout d'une offre
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre'])) {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $competences = trim($_POST['competences']);
    $duree = trim($_POST['duree']);
    $lieu = trim($_POST['lieu']);

    if ($titre && $duree && $lieu) {
        $stmt = $pdo->prepare("
            INSERT INTO OFFRE_STAGE (id_entreprise, titre, description, competences, duree, lieu)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$entreprise['id_entreprise'], $titre, $description, $competences, $duree, $lieu]);
        $message = 'Offre publiée avec succès !';
        header('Location: dashboard_entreprise.php?ok=1');
        exit;
    } else {
        $message = 'Veuillez remplir tous les champs obligatoires.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Entreprise — <?= htmlspecialchars($entreprise['nom_entreprise']) ?></title>
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

    .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 28px; }

    .kpi-card { background: white; border-radius: 12px; padding: 20px 24px; border: 0.5px solid var(--bordure); }
    .kpi-label { font-size: 12px; color: var(--gris); font-weight: 500; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
    .kpi-valeur { font-size: 28px; font-weight: 600; color: var(--texte); margin-bottom: 4px; }
    .kpi-sous { font-size: 12px; color: var(--gris); }

    .section-card { background: white; border-radius: 12px; padding: 24px; border: 0.5px solid var(--bordure); margin-bottom: 28px; }
    .section-titre { font-size: 15px; font-weight: 600; color: var(--texte); margin-bottom: 16px; }

    .form-label-sm { font-size: 12px; font-weight: 500; color: var(--gris); margin-bottom: 5px; display: block; }

    .form-control, .form-select {
      border: 1.5px solid var(--bordure); border-radius: 8px; padding: 9px 12px;
      font-size: 13px; font-family: 'DM Sans', sans-serif; color: var(--texte); background: #fafcff;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--bleu-clair); box-shadow: 0 0 0 3px rgba(45,106,159,0.1); outline: none;
    }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }

    .btn-publier {
      padding: 10px 24px; background: var(--bleu); color: white; border: none;
      border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 14px;
      font-weight: 600; cursor: pointer;
    }

    .btn-publier:hover { background: var(--bleu-clair); }

    .alerte-succes {
      background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
      padding: 12px 16px; font-size: 13px; color: #16a34a; margin-bottom: 20px;
    }

    .alerte-erreur {
      background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px;
      padding: 12px 16px; font-size: 13px; color: #dc2626; margin-bottom: 20px;
    }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead th { padding: 10px 12px; background: var(--bg); color: var(--gris); font-weight: 500; font-size: 12px; text-align: left; border-bottom: 1px solid var(--bordure); }
    tbody td { padding: 12px; border-bottom: 0.5px solid var(--bordure); vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafcff; }

    .badge-attente { font-size: 11px; padding: 3px 10px; border-radius: 20px; background: #fef9c3; color: #a16207; font-weight: 500; }
    .badge-validee { font-size: 11px; padding: 3px 10px; border-radius: 20px; background: #f0fdf4; color: #16a34a; font-weight: 500; }
    .badge-refusee { font-size: 11px; padding: 3px 10px; border-radius: 20px; background: #fef2f2; color: #dc2626; font-weight: 500; }

    .btn-action {
      font-size: 12px; padding: 5px 12px; border-radius: 6px; border: 1.5px solid var(--bleu);
      color: var(--bleu); background: white; font-family: 'DM Sans', sans-serif;
      font-weight: 500; cursor: pointer; text-decoration: none;
    }

    .btn-action:hover { background: var(--bleu); color: white; }

    .vide { text-align: center; padding: 32px; color: var(--gris); font-size: 14px; }

    @media (max-width: 992px) {
      .sidebar { display: none; }
      .main { margin-left: 0; padding: 20px; }
      .kpi-grid { grid-template-columns: 1fr 1fr; }
      .form-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<nav class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-carre">CY</div>
    <span class="logo-texte">Plateforme Stages</span>
  </div>

  <a href="dashboard_entreprise.php" class="nav-item active">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
      <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
    </svg>
    Tableau de bord
  </a>

  <a href="mes_offres.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
      <polyline points="14,2 14,8 20,8"/>
    </svg>
    Mes offres
  </a>

  <a href="candidatures.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    Candidatures
  </a>

  <a href="conventions.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <polyline points="9,11 12,14 22,4"/>
      <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
    </svg>
    Conventions
  </a>

  <div class="sidebar-footer">
    <div class="user-info">
      <div class="avatar">
        <?= strtoupper(substr($entreprise['nom_entreprise'], 0, 2)) ?>
      </div>
      <div>
        <div class="user-nom"><?= htmlspecialchars($entreprise['nom_entreprise']) ?></div>
        <div class="user-role"><?= htmlspecialchars($entreprise['secteur'] ?? 'Entreprise') ?></div>
      </div>
    </div>
    <a href="../backend/logout.php" class="btn-deconnexion">Déconnexion</a>
  </div>
</nav>

<main class="main">

  <div class="page-header">
    <h1>Espace <?= htmlspecialchars($entreprise['nom_entreprise']) ?></h1>
    <p>Gérez vos offres de stage et suivez les candidatures reçues.</p>
  </div>

  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Offres publiées</div>
      <div class="kpi-valeur"><?= $nb_offres ?></div>
      <div class="kpi-sous">au total</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Candidatures reçues</div>
      <div class="kpi-valeur"><?= $nb_candidatures ?></div>
      <div class="kpi-sous">toutes offres confondues</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">En attente</div>
      <div class="kpi-valeur"><?= $nb_en_attente ?></div>
      <div class="kpi-sous">à traiter</div>
    </div>
  </div>

  <!-- FORMULAIRE NOUVELLE OFFRE -->
  <div class="section-card">
    <div class="section-titre">Publier une nouvelle offre de stage</div>

    <?php if (isset($_GET['ok'])): ?>
      <div class="alerte-succes">Offre publiée avec succès !</div>
    <?php elseif ($message): ?>
      <div class="alerte-erreur"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-grid">
        <div>
          <label class="form-label-sm">Intitulé du poste *</label>
          <input type="text" name="titre" class="form-control" placeholder="Développeur Web PHP" required>
        </div>
        <div>
          <label class="form-label-sm">Compétences requises</label>
          <input type="text" name="competences" class="form-control" placeholder="PHP, MySQL, Bootstrap">
        </div>
        <div>
          <label class="form-label-sm">Durée *</label>
          <select name="duree" class="form-select" required>
            <option value="">Choisir</option>
            <option>1 mois</option>
            <option>2 mois</option>
            <option>3 mois</option>
            <option>6 mois</option>
          </select>
        </div>
        <div>
          <label class="form-label-sm">Lieu *</label>
          <input type="text" name="lieu" class="form-control" placeholder="Paris, Remote..." required>
        </div>
      </div>
      <div style="margin-bottom:14px;">
        <label class="form-label-sm">Description du poste</label>
        <textarea name="description" class="form-control" rows="3" placeholder="Décrivez les missions..."></textarea>
      </div>
      <button type="submit" class="btn-publier">Publier l'offre</button>
    </form>
  </div>

  <!-- CANDIDATURES REÇUES -->
  <div class="section-card">
    <div class="section-titre">Candidatures reçues</div>
    <?php if (empty($candidatures)): ?>
      <div class="vide">Aucune candidature reçue pour l'instant.</div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Étudiant</th>
            <th>Filière</th>
            <th>Offre</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($candidatures as $c): ?>
            <tr>
              <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
              <td><?= htmlspecialchars($c['filiere']) ?></td>
              <td><?= htmlspecialchars($c['titre_offre']) ?></td>
              <td><?= date('d/m/Y', strtotime($c['date_candidature'])) ?></td>
              <td>
                <?php if ($c['statut'] === 'en_attente'): ?>
                  <span class="badge-attente">En attente</span>
                <?php elseif ($c['statut'] === 'validee'): ?>
                  <span class="badge-validee">Validée</span>
                <?php else: ?>
                  <span class="badge-refusee">Refusée</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="candidature_detail.php?id=<?= $c['id'] ?>" class="btn-action">Voir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
