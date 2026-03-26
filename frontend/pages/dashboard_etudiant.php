<?php
session_start();
require_once '../../backend/connexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: login.php');
    exit;
}

$id_utilisateur = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT u.nom, u.prenom, u.email, e.filiere, e.promotion, e.id as id_etudiant
    FROM UTILISATEUR u
    JOIN ETUDIANT e ON e.id_utilisateur = u.id
    WHERE u.id = ?
");
$stmt->execute([$id_utilisateur]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM CANDIDATURE WHERE id_etudiant = ?");
$stmt->execute([$etudiant['id_etudiant']]);
$nb_candidatures = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM DOCUMENT d
    JOIN CANDIDATURE c ON c.id = d.id_candidature
    WHERE c.id_etudiant = ?
");
$stmt->execute([$etudiant['id_etudiant']]);
$nb_documents = $stmt->fetchColumn();

$q     = isset($_GET['q'])    ? trim($_GET['q'])    : '';
$duree = isset($_GET['duree'])? trim($_GET['duree']): '';
$lieu  = isset($_GET['lieu']) ? trim($_GET['lieu']) : '';

$sql = "SELECT o.*, e.nom_entreprise FROM OFFRE_STAGE o JOIN ENTREPRISE e ON e.id = o.id_entreprise WHERE 1=1";
$params = [];

if ($q) {
    $sql .= " AND (o.titre LIKE ? OR o.competences LIKE ? OR o.description LIKE ?)";
    $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%";
}
if ($duree) {
    $sql .= " AND o.duree = ?";
    $params[] = $duree;
}
if ($lieu) {
    $sql .= " AND o.lieu LIKE ?";
    $params[] = "%$lieu%";
}

$sql .= " ORDER BY o.date_publication DESC LIMIT 6";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$offres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT id_offre FROM CANDIDATURE WHERE id_etudiant = ?");
$stmt->execute([$etudiant['id_etudiant']]);
$offres_postulees = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Étudiant — Plateforme Stages CY Tech</title>
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
      font-family: 'DM Serif Display', serif; font-size: 15px;
      color: var(--bleu); font-weight: 700;
    }

    .logo-texte { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.9); }

    .nav-item {
      display: flex; align-items: center; gap: 12px;
      padding: 11px 24px; color: rgba(255,255,255,0.65);
      text-decoration: none; font-size: 14px; font-weight: 500;
      transition: all 0.15s; border-left: 3px solid transparent;
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
      width: 100%; padding: 8px;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 6px; color: rgba(255,255,255,0.7);
      font-size: 13px; font-family: 'DM Sans', sans-serif;
      cursor: pointer; text-align: center;
      text-decoration: none; display: block; transition: all 0.15s;
    }

    .btn-deconnexion:hover { background: rgba(255,255,255,0.14); color: white; }

    .main { margin-left: 240px; padding: 32px; min-height: 100vh; }

    .page-header { margin-bottom: 28px; }
    .page-header h1 { font-family: 'DM Serif Display', serif; font-size: 26px; margin-bottom: 4px; }
    .page-header p { font-size: 14px; color: var(--gris); }

    .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 28px; }

    .kpi-card {
      background: white; border-radius: 12px;
      padding: 20px 24px; border: 0.5px solid var(--bordure);
    }

    .kpi-label { font-size: 12px; color: var(--gris); font-weight: 500; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
    .kpi-valeur { font-size: 28px; font-weight: 600; margin-bottom: 4px; }
    .kpi-sous { font-size: 12px; color: var(--gris); }

    .section-card {
      background: white; border-radius: 12px;
      padding: 24px; border: 0.5px solid var(--bordure); margin-bottom: 28px;
    }

    .section-titre { font-size: 15px; font-weight: 600; margin-bottom: 16px; }

    .recherche-grid { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 10px; align-items: end; }

    .form-label-sm { font-size: 12px; font-weight: 500; color: var(--gris); margin-bottom: 5px; display: block; }

    .form-control, .form-select {
      border: 1.5px solid var(--bordure); border-radius: 8px;
      padding: 9px 12px; font-size: 13px;
      font-family: 'DM Sans', sans-serif; color: var(--texte); background: #fafcff;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--bleu-clair);
      box-shadow: 0 0 0 3px rgba(45,106,159,0.1); outline: none;
    }

    .btn-chercher {
      padding: 9px 20px; background: var(--bleu); color: white;
      border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif;
      font-size: 13px; font-weight: 600; cursor: pointer;
    }

    .btn-chercher:hover { background: var(--bleu-clair); }

    .offres-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }

    .offre-card {
      background: white; border: 0.5px solid var(--bordure);
      border-radius: 10px; padding: 18px 20px; transition: border-color 0.15s;
    }

    .offre-card:hover { border-color: var(--bleu-clair); }

    .offre-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
    .offre-titre { font-size: 14px; font-weight: 600; }
    .offre-entreprise { font-size: 13px; color: var(--gris); margin-bottom: 10px; }

    .badge-statut { font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 500; }
    .badge-nouveau { background: #e8f0f7; color: var(--bleu-clair); }
    .badge-postule { background: #f0fdf4; color: #16a34a; }

    .offre-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }

    .tag {
      font-size: 11px; padding: 3px 10px; border-radius: 20px;
      background: var(--bg); color: var(--gris); border: 0.5px solid var(--bordure);
    }

    .offre-footer { display: flex; justify-content: space-between; align-items: center; }
    .offre-date { font-size: 11px; color: var(--gris); }

    .btn-voir {
      font-size: 12px; padding: 6px 14px; border-radius: 6px;
      border: 1.5px solid var(--bleu); color: var(--bleu); background: white;
      font-family: 'DM Sans', sans-serif; font-weight: 600;
      cursor: pointer; text-decoration: none; transition: all 0.15s;
    }

    .btn-voir:hover { background: var(--bleu); color: white; }

    .btn-postuler {
      font-size: 12px; padding: 6px 14px; border-radius: 6px;
      border: none; background: var(--bleu); color: white;
      font-family: 'DM Sans', sans-serif; font-weight: 600; cursor: pointer;
    }

    .btn-postuler:hover { background: var(--bleu-clair); }
    .aucune-offre { text-align: center; padding: 40px; color: var(--gris); font-size: 14px; }

    @media (max-width: 992px) {
      .sidebar { display: none; }
      .main { margin-left: 0; padding: 20px; }
      .kpi-grid { grid-template-columns: 1fr 1fr; }
      .recherche-grid { grid-template-columns: 1fr 1fr; }
      .offres-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<nav class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-carre">CY</div>
    <span class="logo-texte">Plateforme Stages</span>
  </div>
  <a href="dashboard_etudiant.php" class="nav-item active">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
      <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
    </svg>
    Tableau de bord
  </a>
  <a href="offres.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
    </svg>
    Offres de stage
  </a>
  <a href="mon_dossier.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
      <polyline points="14,2 14,8 20,8"/>
    </svg>
    Mon dossier
  </a>
  <a href="documents.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
      <polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/>
    </svg>
    Documents
  </a>
  <a href="profil.php" class="nav-item">
    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
      <circle cx="12" cy="7" r="4"/>
    </svg>
    Mon profil
  </a>
  <div class="sidebar-footer">
    <div class="user-info">
      <div class="avatar">
        <?php echo strtoupper(substr($etudiant['prenom'], 0, 1) . substr($etudiant['nom'], 0, 1)); ?>
      </div>
      <div>
        <div class="user-nom"><?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></div>
        <div class="user-role">Étudiant · <?php echo htmlspecialchars($etudiant['promotion']); ?></div>
      </div>
    </div>
    <a href="../../backend/logout.php" class="btn-deconnexion">Déconnexion</a>
  </div>
</nav>

<main class="main">
  <div class="page-header">
    <h1>Bonjour, <?php echo htmlspecialchars($etudiant['prenom']); ?> 👋</h1>
    <p>Voici un résumé de votre activité sur la plateforme.</p>
  </div>

  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Candidatures envoyées</div>
      <div class="kpi-valeur"><?php echo $nb_candidatures; ?></div>
      <div class="kpi-sous">depuis votre inscription</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Offres disponibles</div>
      <div class="kpi-valeur"><?php echo count($offres); ?></div>
      <div class="kpi-sous">correspondant à votre recherche</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Documents déposés</div>
      <div class="kpi-valeur"><?php echo $nb_documents; ?></div>
      <div class="kpi-sous">sur votre espace</div>
    </div>
  </div>

  <div class="section-card">
    <div class="section-titre">Rechercher une offre de stage</div>
    <form method="GET" action="">
      <div class="recherche-grid">
        <div>
          <label class="form-label-sm">Mots-clés</label>
          <input type="text" name="q" class="form-control"
            placeholder="PHP, Marketing..."
            value="<?php echo htmlspecialchars($q); ?>">
        </div>
        <div>
          <label class="form-label-sm">Durée</label>
          <select name="duree" class="form-select">
            <option value="">Toutes</option>
            <?php foreach (['1 mois','2 mois','3 mois','6 mois'] as $d): ?>
              <option <?php echo $duree === $d ? 'selected' : ''; ?>><?php echo $d; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label-sm">Lieu</label>
          <input type="text" name="lieu" class="form-control"
            placeholder="Paris, Remote..."
            value="<?php echo htmlspecialchars($lieu); ?>">
        </div>
        <div>
          <label class="form-label-sm">&nbsp;</label>
          <button type="submit" class="btn-chercher">Rechercher</button>
        </div>
      </div>
    </form>
  </div>

  <div class="section-titre" style="margin-bottom:14px;">Offres disponibles</div>

  <?php if (empty($offres)): ?>
    <div class="aucune-offre">Aucune offre ne correspond à votre recherche.</div>
  <?php else: ?>
  <div class="offres-grid">
    <?php foreach ($offres as $offre): ?>
    <div class="offre-card">
      <div class="offre-header">
        <div class="offre-titre"><?php echo htmlspecialchars($offre['titre']); ?></div>
        <?php if (in_array($offre['id'], $offres_postulees)): ?>
          <span class="badge-statut badge-postule">Postulé</span>
        <?php else: ?>
          <span class="badge-statut badge-nouveau">Nouveau</span>
        <?php endif; ?>
      </div>
      <div class="offre-entreprise">
        <?php echo htmlspecialchars($offre['nom_entreprise']); ?> · <?php echo htmlspecialchars($offre['lieu']); ?>
      </div>
      <div class="offre-tags">
        <?php foreach (array_slice(explode(',', $offre['competences']), 0, 3) as $comp): ?>
          <span class="tag"><?php echo htmlspecialchars(trim($comp)); ?></span>
        <?php endforeach; ?>
        <span class="tag"><?php echo htmlspecialchars($offre['duree']); ?></span>
      </div>
      <div class="offre-footer">
        <span class="offre-date">Publié le <?php echo date('d/m/Y', strtotime($offre['date_publication'])); ?></span>
        <?php if (in_array($offre['id'], $offres_postulees)): ?>
          <a href="offre_detail.php?id=<?php echo $offre['id']; ?>" class="btn-voir">Voir</a>
        <?php else: ?>
          <form method="POST" action="postuler.php" style="display:inline;">
            <input type="hidden" name="id_offre" value="<?php echo $offre['id']; ?>">
            <button type="submit" class="btn-postuler">Postuler</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
