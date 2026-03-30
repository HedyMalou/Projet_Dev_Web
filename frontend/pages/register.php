<?php
require_once '../../backend/connexion.php';

$erreur  = '';
$succes  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom        = trim($_POST['nom'] ?? '');
    $prenom     = trim($_POST['prenom'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $mdp        = trim($_POST['mot_de_passe'] ?? '');
    $mdp2       = trim($_POST['mot_de_passe2'] ?? '');
    $role       = trim($_POST['role'] ?? '');

    // Champs spécifiques selon le rôle
    $filiere         = trim($_POST['filiere'] ?? '');
    $promotion       = trim($_POST['promotion'] ?? '');
    $numero_etudiant = trim($_POST['numero_etudiant'] ?? '');
    $departement     = trim($_POST['departement'] ?? '');
    $specialite      = trim($_POST['specialite'] ?? '');
    $nom_entreprise  = trim($_POST['nom_entreprise'] ?? '');
    $secteur         = trim($_POST['secteur'] ?? '');
    $adresse         = trim($_POST['adresse'] ?? '');

    if (!$nom || !$prenom || !$email || !$mdp || !$role) {
        $erreur = 'Veuillez remplir tous les champs obligatoires.';
    } elseif ($mdp !== $mdp2) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($mdp) < 8) {
        $erreur = 'Le mot de passe doit contenir au moins 8 caractères.';
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM UTILISATEUR WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = 'Cette adresse email est déjà utilisée.';
        } else {
            $hash = password_hash($mdp, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $hash, $role]);
            $id_user = $pdo->lastInsertId();

            // Insertion dans la table spécifique selon le rôle
            if ($role === 'etudiant') {
                $stmt = $pdo->prepare("INSERT INTO ETUDIANT (id_utilisateur, filiere, promotion, numero_etudiant) VALUES (?, ?, ?, ?)");
                $stmt->execute([$id_user, $filiere, $promotion, $numero_etudiant]);
            } elseif ($role === 'tuteur') {
                $stmt = $pdo->prepare("INSERT INTO TUTEUR (id_utilisateur, departement) VALUES (?, ?)");
                $stmt->execute([$id_user, $departement]);
            } elseif ($role === 'jury') {
                $stmt = $pdo->prepare("INSERT INTO JURY (id_utilisateur, specialite) VALUES (?, ?)");
                $stmt->execute([$id_user, $specialite]);
            } elseif ($role === 'entreprise') {
                $stmt = $pdo->prepare("INSERT INTO ENTREPRISE (id_utilisateur, nom_entreprise, secteur, adresse) VALUES (?, ?, ?, ?)");
                $stmt->execute([$id_user, $nom_entreprise, $secteur, $adresse]);
            }

            $succes = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription — Plateforme Stages CY Tech</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <style>
    :root {
      --bleu: #1a3a5c; --bleu-clair: #2d6a9f;
      --bordure: #d1dce8; --texte: #1c1c1c; --gris: #6b7280;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DM Sans', sans-serif; background: #f0f4f8; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 32px 16px; }
    .carte { background: white; border-radius: 16px; padding: 40px; width: 100%; max-width: 520px; }
    .logo-zone { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
    .logo-carre { width: 34px; height: 34px; background: var(--bleu); border-radius: 7px; display: flex; align-items: center; justify-content: center; font-family: 'DM Serif Display', serif; font-size: 15px; color: white; }
    .logo-texte { font-size: 13px; font-weight: 600; color: var(--bleu); }
    h2 { font-family: 'DM Serif Display', serif; font-size: 24px; color: var(--texte); margin-bottom: 6px; }
    .sous-titre { font-size: 14px; color: var(--gris); margin-bottom: 28px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-full { grid-column: 1 / -1; }
    .form-label-sm { font-size: 12px; font-weight: 500; color: var(--gris); margin-bottom: 5px; display: block; }
    .form-control, .form-select { border: 1.5px solid var(--bordure); border-radius: 8px; padding: 9px 12px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: var(--texte); background: #fafcff; width: 100%; }
    .form-control:focus, .form-select:focus { border-color: var(--bleu-clair); box-shadow: 0 0 0 3px rgba(45,106,159,0.1); outline: none; }
    .champs-role { display: none; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--bordure); }
    .champs-role.visible { display: block; }
    .separateur { font-size: 12px; font-weight: 600; color: var(--gris); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 14px; }
    .btn-inscrire { width: 100%; padding: 13px; background: var(--bleu); color: white; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 20px; }
    .btn-inscrire:hover { background: var(--bleu-clair); }
    .alerte-erreur { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #dc2626; margin-bottom: 20px; }
    .alerte-succes { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #16a34a; margin-bottom: 20px; }
    .lien-connexion { text-align: center; font-size: 13px; color: var(--gris); margin-top: 20px; }
    .lien-connexion a { color: var(--bleu-clair); text-decoration: none; font-weight: 500; }
    .lien-connexion a:hover { text-decoration: underline; }
    .mb-14 { margin-bottom: 14px; }
    @media (max-width: 576px) { .form-grid { grid-template-columns: 1fr; } .carte { padding: 28px 20px; } }
  </style>
</head>
<body>
<div class="carte">
  <div class="logo-zone">
    <div class="logo-carre">CY</div>
    <span class="logo-texte">CY Tech — Plateforme Stages</span>
  </div>

  <h2>Créer un compte</h2>
  <p class="sous-titre">Rejoignez la plateforme de gestion des stages.</p>

  <?php if ($erreur): ?>
    <div class="alerte-erreur"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>

  <?php if ($succes): ?>
    <div class="alerte-succes">Compte créé avec succès ! <a href="login.html" style="color:#16a34a;font-weight:600;">Se connecter</a></div>
  <?php else: ?>

  <form method="POST">
    <div class="form-grid">
      <div>
        <label class="form-label-sm">Prénom *</label>
        <input type="text" name="prenom" class="form-control" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label-sm">Nom *</label>
        <input type="text" name="nom" class="form-control" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
      </div>
      <div class="form-full">
        <label class="form-label-sm">Adresse email *</label>
        <input type="email" name="email" class="form-control" required placeholder="prenom.nom@etu.cyu.fr" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label-sm">Mot de passe *</label>
        <input type="password" name="mot_de_passe" class="form-control" required placeholder="8 caractères min.">
      </div>
      <div>
        <label class="form-label-sm">Confirmer *</label>
        <input type="password" name="mot_de_passe2" class="form-control" required placeholder="Répéter le mot de passe">
      </div>
      <div class="form-full">
        <label class="form-label-sm">Rôle *</label>
        <select name="role" class="form-select" id="role-select" required>
          <option value="" disabled selected>Sélectionnez votre rôle</option>
          <option value="etudiant"   <?= (($_POST['role'] ?? '') === 'etudiant')   ? 'selected' : '' ?>>Étudiant</option>
          <option value="tuteur"     <?= (($_POST['role'] ?? '') === 'tuteur')     ? 'selected' : '' ?>>Tuteur / Professeur</option>
          <option value="jury"       <?= (($_POST['role'] ?? '') === 'jury')       ? 'selected' : '' ?>>Jury</option>
          <option value="entreprise" <?= (($_POST['role'] ?? '') === 'entreprise') ? 'selected' : '' ?>>Entreprise</option>
        </select>
      </div>
    </div>

    <!-- Champs étudiant -->
    <div class="champs-role <?= (($_POST['role'] ?? '') === 'etudiant') ? 'visible' : '' ?>" id="champs-etudiant">
      <div class="separateur">Informations étudiant</div>
      <div class="form-grid">
        <div>
          <label class="form-label-sm">Filière</label>
          <select name="filiere" class="form-select">
            <option value="">Choisir</option>
            <option>Informatique</option>
            <option>Mathématiques</option>
            <option>Génie Civil</option>
            <option>Electronique</option>
          </select>
        </div>
        <div>
          <label class="form-label-sm">Promotion</label>
          <select name="promotion" class="form-select">
            <option value="">Choisir</option>
            <option>ING1</option>
            <option>ING2</option>
            <option>ING3</option>
          </select>
        </div>
        <div class="form-full">
          <label class="form-label-sm">Numéro étudiant</label>
          <input type="text" name="numero_etudiant" class="form-control" placeholder="Ex: 12345678" value="<?= htmlspecialchars($_POST['numero_etudiant'] ?? '') ?>">
        </div>
      </div>
    </div>

    <!-- Champs tuteur -->
    <div class="champs-role <?= (($_POST['role'] ?? '') === 'tuteur') ? 'visible' : '' ?>" id="champs-tuteur">
      <div class="separateur">Informations tuteur</div>
      <div class="mb-14">
        <label class="form-label-sm">Département</label>
        <input type="text" name="departement" class="form-control" placeholder="Ex: Informatique" value="<?= htmlspecialchars($_POST['departement'] ?? '') ?>">
      </div>
    </div>

    <!-- Champs jury -->
    <div class="champs-role <?= (($_POST['role'] ?? '') === 'jury') ? 'visible' : '' ?>" id="champs-jury">
      <div class="separateur">Informations jury</div>
      <div class="mb-14">
        <label class="form-label-sm">Spécialité</label>
        <input type="text" name="specialite" class="form-control" placeholder="Ex: Génie Logiciel" value="<?= htmlspecialchars($_POST['specialite'] ?? '') ?>">
      </div>
    </div>

    <!-- Champs entreprise -->
    <div class="champs-role <?= (($_POST['role'] ?? '') === 'entreprise') ? 'visible' : '' ?>" id="champs-entreprise">
      <div class="separateur">Informations entreprise</div>
      <div class="form-grid">
        <div class="form-full">
          <label class="form-label-sm">Nom de l'entreprise</label>
          <input type="text" name="nom_entreprise" class="form-control" placeholder="Ex: TechCorp" value="<?= htmlspecialchars($_POST['nom_entreprise'] ?? '') ?>">
        </div>
        <div>
          <label class="form-label-sm">Secteur</label>
          <input type="text" name="secteur" class="form-control" placeholder="Ex: Informatique" value="<?= htmlspecialchars($_POST['secteur'] ?? '') ?>">
        </div>
        <div>
          <label class="form-label-sm">Adresse</label>
          <input type="text" name="adresse" class="form-control" placeholder="Ex: Paris" value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
        </div>
      </div>
    </div>

    <button type="submit" class="btn-inscrire">Créer mon compte</button>
  </form>

  <?php endif; ?>

  <div class="lien-connexion">
    Déjà un compte ? <a href="login.html">Se connecter</a>
  </div>
</div>

<script>
  const roleSelect = document.getElementById('role-select');
  const champsRoles = {
    'etudiant':   document.getElementById('champs-etudiant'),
    'tuteur':     document.getElementById('champs-tuteur'),
    'jury':       document.getElementById('champs-jury'),
    'entreprise': document.getElementById('champs-entreprise'),
  };

  roleSelect.addEventListener('change', function() {
    Object.values(champsRoles).forEach(el => el.classList.remove('visible'));
    if (champsRoles[this.value]) {
      champsRoles[this.value].classList.add('visible');
    }
  });
</script>
</body>
</html>
