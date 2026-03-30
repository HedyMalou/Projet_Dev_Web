<?php
session_start();
require_once '../../backend/connexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header('Location: login.html');
    exit;
}

$id_utilisateur = $_SESSION['user_id'];

// Infos étudiant
$stmt = $pdo->prepare("
    SELECT u.nom, u.prenom, u.email, e.filiere, e.promotion, e.id as id_etudiant
    FROM UTILISATEUR u
    JOIN ETUDIANT e ON e.id_utilisateur = u.id
    WHERE u.id = ?
");
$stmt->execute([$id_utilisateur]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    header('Location: login.html');
    exit;
}

// Candidatures de l'étudiant avec infos offre et entreprise
$stmt = $pdo->prepare("
    SELECT c.id, c.statut, c.date_candidature,
           o.titre, o.lieu, o.duree,
           ent.nom_entreprise,
           conv.id as id_convention,
           conv.statut_etudiant, conv.statut_entreprise, conv.statut_tuteur
    FROM CANDIDATURE c
    JOIN OFFRE_STAGE o ON o.id = c.id_offre
    JOIN ENTREPRISE ent ON ent.id = o.id_entreprise
    LEFT JOIN CONVENTION conv ON conv.id_candidature = c.id
    WHERE c.id_etudiant = ?
    ORDER BY c.date_candidature DESC
");
$stmt->execute([$etudiant['id_etudiant']]);
$candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les documents déposés pour chaque candidature
$stmt = $pdo->prepare("
    SELECT d.*, c.id as id_cand
    FROM DOCUMENT d
    JOIN CANDIDATURE c ON c.id = d.id_candidature
    WHERE c.id_etudiant = ?
    ORDER BY d.date_depot DESC
");
$stmt->execute([$etudiant['id_etudiant']]);
$tous_documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Indexer les documents par candidature
$documents_par_cand = [];
foreach ($tous_documents as $doc) {
    $documents_par_cand[$doc['id_cand']][] = $doc;
}

// Commentaires du tuteur
$stmt = $pdo->prepare("
    SELECT com.contenu, com.date, u.nom, u.prenom, u.role
    FROM COMMENTAIRE com
    JOIN CANDIDATURE c ON c.id = com.id_candidature
    JOIN UTILISATEUR u ON u.id = com.id_utilisateur
    WHERE c.id_etudiant = ?
    ORDER BY com.date DESC
");
$stmt->execute([$etudiant['id_etudiant']]);
$commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels_statut = [
    'en_attente' => ['texte' => 'En attente',  'classe' => 'badge-attente'],
    'validee'    => ['texte' => 'Validée',      'classe' => 'badge-validee'],
    'refusee'    => ['texte' => 'Refusée',      'classe' => 'badge-refusee'],
    'archivee'   => ['texte' => 'Archivée',     'classe' => 'badge-archive'],
];

$labels_type = [
    'rapport'          => 'Rapport de stage',
    'resume'           => 'Résumé',
    'fiche_evaluation' => 'Fiche d\'évaluation',
    'convention'       => 'Convention',
    'autre'            => 'Autre',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon dossier — Plateforme Stages CY Tech</title>
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
      text-decoration: none; display: block;
    }

    .btn-deconnexion:hover { background: rgba(255,255,255,0.14); color: white; }

    .main { margin-left: 240px; padding: 32px; min-height: 100vh; }

    .page-header { margin-bottom: 28px; }
    .page-header h1 { font-family: 'DM Serif Display', serif; font-size: 26px; margin-bottom: 4px; }
    .page-header p { font-size: 14px; color: var(--gris); }

    .section-card {
      background: white; border-radius: 12px;
      padding: 24px; border: 0.5px solid var(--bordure); margin-bottom: 24px;
    }

    .section-titre { font-size: 15px; font-weight: 600; margin-bottom: 16px; }

    .candidature-bloc {
      border: 1px solid var(--bordure); border-radius: 10px;
      padding: 18px 20px; margin-bottom: 16px;
    }

    .candidature-bloc:last-child { margin-bottom: 0; }

    .candidature-header {
      display: flex; justify-content: space-between;
      align-items: flex-start; margin-bottom: 10px;
    }

    .candidature-titre { font-size: 14px; font-weight: 600; }
    .candidature-meta { font-size: 13px; color: var(--gris); margin-bottom: 12px; }

    .badge-statut { font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 500; }
    .badge-attente { background: #fef9c3; color: #a16207; }
    .badge-validee { background: #f0fdf4; color: #16a34a; }
    .badge-refusee { background: #fef2f2; color: #dc2626; }
    .badge-archive { background: #f3f4f6; color: #6b7280; }

    .convention-bloc {
      background: var(--bg); border-radius: 8px;
      padding: 12px 16px; margin-top: 12px; font-size: 13px;
    }

    .convention-titre { font-weight: 600; margin-bottom: 8px; color: var(--texte); }

    .convention-signataires {
      display: flex; gap: 10px; flex-wrap: wrap;
    }

    .signataire {
      display: flex; align-items: center; gap: 6px;
      font-size: 12px; color: var(--gris);
    }

    .dot-signe { width: 8px; height: 8px; border-radius: 50%; background: #16a34a; }
    .dot-attente { width: 8px; height: 8px; border-radius: 50%; background: #d1dce8; }

    .btn-signer {
      font-size: 12px; padding: 5px 14px; border-radius: 6px;
      background: var(--bleu); color: white; border: none;
      font-family: 'DM Sans', sans-serif; font-weight: 500;
      cursor: pointer; margin-top: 10px;
    }

    .btn-signer:hover { background: var(--bleu-clair); }

    .docs-liste { margin-top: 12px; }

    .doc-item {
      display: flex; align-items: center; justify-content: space-between;
      padding: 8px 0; border-bottom: 0.5px solid var(--bordure); font-size: 13px;
    }

    .doc-item:last-child { border-bottom: none; }

    .doc-type { font-weight: 500; }
    .doc-date { font-size: 11px; color: var(--gris); }

    .btn-dl {
      font-size: 12px; padding: 4px 12px; border-radius: 6px;
      border: 1.5px solid var(--bleu); color: var(--bleu); background: white;
      font-family: 'DM Sans', sans-serif; text-decoration: none; font-weight: 500;
    }

    .btn-dl:hover { background: var(--bleu); color: white; }

    .upload-form { margin-top: 14px; }

    .form-label-sm { font-size: 12px; font-weight: 500; color: var(--gris); margin-bottom: 5px; display: block; }

    .form-control, .form-select {
      border: 1.5px solid var(--bordure); border-radius: 8px;
      padding: 9px 12px; font-size: 13px;
      font-family: 'DM Sans', sans-serif; background: #fafcff;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--bleu-clair);
      box-shadow: 0 0 0 3px rgba(45,106,159,0.1); outline: none;
    }

    .upload-grid { display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end; }

    .btn-deposer {
      padding: 9px 18px; background: var(--bleu); color: white;
      border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif;
      font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap;
    }

    .btn-deposer:hover { background: var(--bleu-clair); }

    .alerte-succes {
      background: #f0fdf4; border: 1px solid #bbf7d0;
      border-radius: 8px; padding: 10px 14px;
      font-size: 13px; color: #16a34a; margin-bottom: 16px;
    }

    .alerte-erreur {
      background: #fef2f2; border: 1px solid #fecaca;
      border-radius: 8px; padding: 10px 14px;
      font-size: 13px; color: #dc2626; margin-bottom: 16px;
    }

    .commentaire-item {
      padding: 12px 0; border-bottom: 0.5px solid var(--bordure); font-size: 13px;
    }

    .commentaire-item:last-child { border-bottom: none; }

    .commentaire-auteur { font-weight: 600; margin-bottom: 4px; }
    .commentaire-date { font-size: 11px; color: var(--gris); }
    .commentaire-texte { margin-top: 6px; color: var(--texte); line-height: 1.5; }

    .vide { text-align: center; padding: 28px; color: var(--gris); font-size: 14px; }

    @media (max-width: 992px) {
      .sidebar { display: none; }
      .main { margin-left: 0; padding: 20px; }
      .upload-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<nav class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-carre">CY</div>
    <span class="logo-texte">Plateforme Stages</span>
  </div>
  <a href="dashboard_etudiant.php" class="nav-item">
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
  <a href="mon_dossier.php" class="nav-item active">
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
    <h1>Mon dossier</h1>
    <p>Suivez l'état de vos candidatures et déposez vos documents.</p>
  </div>

  <?php if (isset($_GET['ok'])): ?>
    <div class="alerte-succes">
      <?php if ($_GET['ok'] === 'upload'): ?>Document déposé avec succès.
      <?php elseif ($_GET['ok'] === 'convention'): ?>Convention signée.
      <?php endif; ?>
    </div>
  <?php elseif (isset($_GET['erreur'])): ?>
    <div class="alerte-erreur">
      <?php
        $msgs = [
          'fichier'  => 'Erreur lors de l\'envoi du fichier.',
          'format'   => 'Format non accepté. PDF, DOC ou DOCX uniquement.',
          'taille'   => 'Fichier trop lourd (5 Mo maximum).',
          'donnees'  => 'Données invalides.',
          'acces'    => 'Accès refusé.',
          'upload'   => 'Impossible de sauvegarder le fichier.',
        ];
        echo $msgs[$_GET['erreur']] ?? 'Une erreur est survenue.';
      ?>
    </div>
  <?php endif; ?>

  <!-- CANDIDATURES -->
  <div class="section-card">
    <div class="section-titre">Mes candidatures</div>

    <?php if (empty($candidatures)): ?>
      <div class="vide">Vous n'avez pas encore postulé à une offre.</div>
    <?php else: ?>
      <?php foreach ($candidatures as $c): ?>
      <div class="candidature-bloc">

        <div class="candidature-header">
          <div>
            <div class="candidature-titre"><?php echo htmlspecialchars($c['titre']); ?></div>
            <div class="candidature-meta">
              <?php echo htmlspecialchars($c['nom_entreprise']); ?>
              · <?php echo htmlspecialchars($c['lieu']); ?>
              · <?php echo htmlspecialchars($c['duree']); ?>
            </div>
          </div>
          <span class="badge-statut <?php echo $labels_statut[$c['statut']]['classe']; ?>">
            <?php echo $labels_statut[$c['statut']]['texte']; ?>
          </span>
        </div>

        <div style="font-size:12px; color:var(--gris);">
          Candidature envoyée le <?php echo date('d/m/Y', strtotime($c['date_candidature'])); ?>
        </div>

        <?php if ($c['statut'] === 'validee' && $c['id_convention']): ?>
        <!-- CONVENTION -->
        <div class="convention-bloc">
          <div class="convention-titre">Convention de stage</div>
          <div class="convention-signataires">
            <div class="signataire">
              <div class="<?php echo $c['statut_etudiant'] === 'signe' ? 'dot-signe' : 'dot-attente'; ?>"></div>
              Étudiant <?php echo $c['statut_etudiant'] === 'signe' ? '✓' : '(en attente)'; ?>
            </div>
            <div class="signataire">
              <div class="<?php echo $c['statut_entreprise'] === 'signe' ? 'dot-signe' : 'dot-attente'; ?>"></div>
              Entreprise <?php echo $c['statut_entreprise'] === 'signe' ? '✓' : '(en attente)'; ?>
            </div>
            <div class="signataire">
              <div class="<?php echo $c['statut_tuteur'] === 'signe' ? 'dot-signe' : 'dot-attente'; ?>"></div>
              Tuteur <?php echo $c['statut_tuteur'] === 'signe' ? '✓' : '(en attente)'; ?>
            </div>
          </div>
          <?php if ($c['statut_etudiant'] === 'en_attente'): ?>
          <form method="POST" action="../../backend/valider_convention.php">
            <input type="hidden" name="id_convention" value="<?php echo $c['id_convention']; ?>">
            <button type="submit" class="btn-signer">Signer la convention</button>
          </form>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- DOCUMENTS DE CETTE CANDIDATURE -->
        <?php $docs = $documents_par_cand[$c['id']] ?? []; ?>
        <?php if (!empty($docs)): ?>
        <div class="docs-liste">
          <?php foreach ($docs as $doc): ?>
          <div class="doc-item">
            <div>
              <div class="doc-type"><?php echo $labels_type[$doc['type']] ?? $doc['type']; ?></div>
              <div class="doc-date">Déposé le <?php echo date('d/m/Y', strtotime($doc['date_depot'])); ?></div>
            </div>
            <a href="../../backend/telecharger_document.php?id=<?php echo $doc['id']; ?>" class="btn-dl">Télécharger</a>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- FORMULAIRE DÉPÔT DOCUMENT -->
        <?php if (in_array($c['statut'], ['validee', 'archivee'])): ?>
        <div class="upload-form">
          <form method="POST" action="../../backend/uploader_document.php" enctype="multipart/form-data">
            <input type="hidden" name="id_candidature" value="<?php echo $c['id']; ?>">
            <div class="upload-grid">
              <div>
                <label class="form-label-sm">Type de document</label>
                <select name="type" class="form-select" required>
                  <option value="">Choisir</option>
                  <?php foreach ($labels_type as $val => $lab): ?>
                    <option value="<?php echo $val; ?>"><?php echo $lab; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="form-label-sm">Fichier (PDF, DOC, max 5 Mo)</label>
                <input type="file" name="fichier" class="form-control" accept=".pdf,.doc,.docx" required>
              </div>
              <div>
                <label class="form-label-sm">&nbsp;</label>
                <button type="submit" class="btn-deposer">Déposer</button>
              </div>
            </div>
          </form>
        </div>
        <?php endif; ?>

      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- COMMENTAIRES DU TUTEUR -->
  <?php if (!empty($commentaires)): ?>
  <div class="section-card">
    <div class="section-titre">Remarques de mon tuteur / jury</div>
    <?php foreach ($commentaires as $com): ?>
    <div class="commentaire-item">
      <div style="display:flex; justify-content:space-between;">
        <div class="commentaire-auteur">
          <?php echo htmlspecialchars($com['prenom'] . ' ' . $com['nom']); ?>
          <span style="font-size:11px; color:var(--gris); font-weight:400;"> — <?php echo ucfirst($com['role']); ?></span>
        </div>
        <div class="commentaire-date"><?php echo date('d/m/Y', strtotime($com['date'])); ?></div>
      </div>
      <div class="commentaire-texte"><?php echo htmlspecialchars($com['contenu']); ?></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
