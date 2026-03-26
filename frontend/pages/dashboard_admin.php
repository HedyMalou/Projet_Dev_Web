<?php
session_start();
require_once '../backend/connexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id_utilisateur = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT nom, prenom FROM UTILISATEUR WHERE id = ?");
$stmt->execute([$id_utilisateur]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// KPI globaux
$nb_etudiants  = $pdo->query("SELECT COUNT(*) FROM ETUDIANT")->fetchColumn();
$nb_offres     = $pdo->query("SELECT COUNT(*) FROM OFFRE_STAGE")->fetchColumn();
$nb_stages     = $pdo->query("SELECT COUNT(*) FROM CANDIDATURE WHERE statut = 'validee'")->fetchColumn();
$nb_users      = $pdo->query("SELECT COUNT(*) FROM UTILISATEUR")->fetchColumn();

// Liste des utilisateurs
$users = $pdo->query("
    SELECT id, nom, prenom, email, role, created_at
    FROM UTILISATEUR
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Suppression d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_id'])) {
    $id_supp = (int)$_POST['supprimer_id'];
    if ($id_supp !== $id_utilisateur) {
        $stmt = $pdo->prepare("DELETE FROM UTILISATEUR WHERE id = ?");
        $stmt->execute([$id_supp]);
    }
    header('Location: dashboard_admin.php');
    exit;
}

$labels_role = [
    'etudiant'   => 'Étudiant',
    'tuteur'     => 'Tuteur',
    'jury'       => 'Jury',
    'entreprise' => 'Entreprise',
    'admin'      => 'Admin',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin — Plateforme Stages</title>
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

    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }

    .kpi-card { background: white; border-radius: 12px; padding: 20px 24px; border: 0.5px solid var(--bordure); }
    .kpi-label { font-size: 12px; color: var(--gris); font-weight: 500; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
    .kpi-valeur { font-size: 28px; font-weight: 600; color: var(--texte); }

    .section-card { background: white; border-radius: 12px; padding: 24px; border: 0.5px solid var(--bordure); margin-bottom: 28px; }
    .section-titre { font-size: 15px; font-weight: 600; color: var(--texte); margin-bottom: 16px; }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead th { padding: 10px 12px; background: var(--bg); color: var(--gris); font-weight: 500; font-size: 12px; text-align: left; border-bottom: 1px solid var(--bordure); }
    tbody td { padding: 12px; border-bottom: 0.5px solid var(--bordure); vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafcff; }

    .badge-role {
      font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 500;
      background: #e8f0f7; color: var(--bleu-clair);
    }

    .badge-admin { background: #fef9c3; color: #a16207; }

    .btn-supprimer {
      font-size: 12px; padding: 5px 12px; border-radius: 6px;
      border: 1.5px solid #dc2626; color: #dc2626; background: white;
      font-family: 'DM Sans', sans-serif; font-weight: 500; cursor: pointer;
    }

    .btn-supprimer:hover { background: #dc2626; color: white; }

    .vide { text-align: center; padding: 32px; color: var(--gris); font-size: 14px; }

    @media (max-width: 992px) {
      .sidebar { display: none; }
      .main { margin-left: 0; padding: 20px; }
      .kpi-grid { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>
<body>

<nav class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-carre">CY</div>
    <span class="logo-texte">Plateforme Stages</span>
  </div>

  <a href="dashboard_admin.php" class="nav-item active">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
      <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
    </svg>
    Tableau de bord
  </a>

  <a href="gestion_users.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    Utilisateurs
  </a>

  <a href="gestion_offres.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
      <polyline points="14,2 14,8 20,8"/>
    </svg>
    Offres
  </a>

  <a href="archivage.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <polyline points="21,8 21,21 3,21 3,8"/><rect x="1" y="3" width="22" height="5"/>
      <line x1="10" y1="12" x2="14" y2="12"/>
    </svg>
    Archivage
  </a>

  <div class="sidebar-footer">
    <div class="user-info">
      <div class="avatar">
        <?= strtoupper(substr($admin['prenom'], 0, 1) . substr($admin['nom'], 0, 1)) ?>
      </div>
      <div>
        <div class="user-nom"><?= htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']) ?></div>
        <div class="user-role">Administrateur</div>
      </div>
    </div>
    <a href="../backend/logout.php" class="btn-deconnexion">Déconnexion</a>
  </div>
</nav>

<main class="main">

  <div class="page-header">
    <h1>Tableau de bord admin</h1>
    <p>Vue globale de la plateforme.</p>
  </div>

  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Étudiants</div>
      <div class="kpi-valeur"><?= $nb_etudiants ?></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Offres publiées</div>
      <div class="kpi-valeur"><?= $nb_offres ?></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Stages validés</div>
      <div class="kpi-valeur"><?= $nb_stages ?></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Utilisateurs total</div>
      <div class="kpi-valeur"><?= $nb_users ?></div>
    </div>
  </div>

  <div class="section-card">
    <div class="section-titre">Gestion des utilisateurs</div>
    <?php if (empty($users)): ?>
      <div class="vide">Aucun utilisateur.</div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Inscrit le</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <span class="badge-role <?= $u['role'] === 'admin' ? 'badge-admin' : '' ?>">
                  <?= $labels_role[$u['role']] ?? $u['role'] ?>
                </span>
              </td>
              <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
              <td>
                <?php if ($u['id'] !== $id_utilisateur): ?>
                  <form method="POST" style="display:inline;"
                    onsubmit="return confirm('Supprimer cet utilisateur ?')">
                    <input type="hidden" name="supprimer_id" value="<?= $u['id'] ?>">
                    <button type="submit" class="btn-supprimer">Supprimer</button>
                  </form>
                <?php else: ?>
                  <span style="font-size:12px;color:var(--gris);">Vous</span>
                <?php endif; ?>
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
