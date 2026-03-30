<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion — Plateforme Stages CY Tech</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <style>
    :root { --bleu:#1a3a5c; --bleu-clair:#2d6a9f; --accent:#e8f0f7; --texte:#1c1c1c; --gris:#6b7280; --bordure:#d1dce8; }
    * { box-sizing:border-box; margin:0; padding:0; }
    body { font-family:'DM Sans',sans-serif; background:#f0f4f8; min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .page-wrapper { display:grid; grid-template-columns:1fr 1fr; min-height:100vh; width:100%; }

    /* Panneau gauche */
    .panel-gauche { background:var(--bleu); display:flex; flex-direction:column; justify-content:center; align-items:flex-start; padding:60px 56px; position:relative; overflow:hidden; }
    .panel-gauche::before { content:''; position:absolute; top:-80px; right:-80px; width:320px; height:320px; border-radius:50%; background:rgba(255,255,255,0.04); }
    .panel-gauche::after  { content:''; position:absolute; bottom:-60px; left:-60px; width:240px; height:240px; border-radius:50%; background:rgba(255,255,255,0.04); }
    .logo-zone { display:flex; align-items:center; gap:12px; margin-bottom:56px; }
    .logo-carre { width:40px; height:40px; background:white; border-radius:8px; display:flex; align-items:center; justify-content:center; font-family:'DM Serif Display',serif; font-size:18px; color:var(--bleu); font-weight:700; }
    .logo-texte { font-size:15px; font-weight:600; color:rgba(255,255,255,0.9); letter-spacing:0.02em; }
    .panel-gauche h1 { font-family:'DM Serif Display',serif; font-size:36px; color:white; line-height:1.25; margin-bottom:20px; }
    .panel-gauche p { font-size:15px; color:rgba(255,255,255,0.65); line-height:1.7; max-width:340px; margin-bottom:48px; }
    .roles-liste { display:flex; flex-direction:column; gap:10px; }
    .role-badge { display:inline-flex; align-items:center; gap:10px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); border-radius:8px; padding:10px 16px; color:rgba(255,255,255,0.85); font-size:13px; font-weight:500; }
    .role-dot { width:7px; height:7px; border-radius:50%; background:rgba(255,255,255,0.5); }

    /* Panneau droit */
    .panel-droit { background:white; display:flex; flex-direction:column; justify-content:center; align-items:center; padding:60px 48px; }
    .form-zone { width:100%; max-width:400px; }
    .form-zone h2 { font-family:'DM Serif Display',serif; font-size:26px; color:var(--texte); margin-bottom:6px; }
    .sous-titre { font-size:14px; color:var(--gris); margin-bottom:36px; }
    .form-label { font-size:13px; font-weight:500; color:var(--texte); margin-bottom:6px; }
    .form-control, .form-select { border:1.5px solid var(--bordure); border-radius:8px; padding:11px 14px; font-size:14px; font-family:'DM Sans',sans-serif; color:var(--texte); background:#fafcff; transition:border-color 0.2s, box-shadow 0.2s; }
    .form-control:focus, .form-select:focus { border-color:var(--bleu-clair); box-shadow:0 0 0 3px rgba(45,106,159,0.12); background:white; outline:none; }
    .form-control::placeholder { color:#b0bcc8; }
    .btn-connexion { width:100%; padding:13px; background:var(--bleu); color:white; border:none; border-radius:8px; font-family:'DM Sans',sans-serif; font-size:15px; font-weight:600; cursor:pointer; transition:background 0.2s; margin-top:8px; }
    .btn-connexion:hover { background:var(--bleu-clair); }
    .lien-oubli { font-size:13px; color:var(--bleu-clair); text-decoration:none; display:block; text-align:right; margin-top:-4px; margin-bottom:20px; }
    .lien-oubli:hover { text-decoration:underline; }
    .separateur { display:flex; align-items:center; gap:12px; margin:24px 0; color:var(--gris); font-size:12px; }
    .separateur::before, .separateur::after { content:''; flex:1; height:1px; background:var(--bordure); }
    .alerte-erreur { background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:12px 14px; font-size:13px; color:#dc2626; margin-bottom:20px; }
    .alerte-succes { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:12px 14px; font-size:13px; color:#16a34a; margin-bottom:20px; }
    .mb-20 { margin-bottom:20px; }
    .mb-8  { margin-bottom:8px; }
    @media (max-width:768px) { .page-wrapper { grid-template-columns:1fr; } .panel-gauche { display:none; } .panel-droit { padding:40px 24px; } }
  </style>
</head>
<body>
<div class="page-wrapper">

  <!-- Panneau gauche -->
  <div class="panel-gauche">
    <div class="logo-zone">
      <div class="logo-carre">CY</div>
      <span class="logo-texte">CY Tech — Plateforme Stages</span>
    </div>
    <h1>Gérez vos stages en toute simplicité</h1>
    <p>Une plateforme centralisée pour les étudiants, tuteurs, entreprises et jurys de CY Tech.</p>
    <div class="roles-liste">
      <div class="role-badge"><div class="role-dot"></div> Étudiant — recherche &amp; suivi de stage</div>
      <div class="role-badge"><div class="role-dot"></div> Entreprise — dépôt d'offres</div>
      <div class="role-badge"><div class="role-dot"></div> Tuteur — validation &amp; suivi</div>
      <div class="role-badge"><div class="role-dot"></div> Jury — évaluation des rapports</div>
      <div class="role-badge"><div class="role-dot"></div> Administrateur — gestion globale</div>
    </div>
  </div>

  <!-- Panneau droit -->
  <div class="panel-droit">
    <div class="form-zone">
      <h2>Connexion</h2>
      <p class="sous-titre">Entrez vos identifiants pour accéder à votre espace.</p>

      @if (session('succes'))
        <div class="alerte-succes">{{ session('succes') }}</div>
      @endif

      @if ($errors->any())
        <div class="alerte-erreur">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ url('/login') }}" id="formLogin">
        @csrf

        <div class="mb-20">
          <label class="form-label" for="email">Adresse email</label>
          <input type="email" id="email" name="email" class="form-control"
            value="{{ old('email') }}"
            placeholder="prenom.nom@etu.cyu.fr" required autocomplete="email">
        </div>

        <div class="mb-8">
          <label class="form-label" for="mdp">Mot de passe</label>
          <input type="password" id="mdp" name="mot_de_passe" class="form-control"
            placeholder="••••••••" required autocomplete="current-password">
        </div>

        <a href="{{ route('forgot-password') }}" class="lien-oubli">Mot de passe oublié ?</a>

        <div class="mb-20">
          <label class="form-label" for="role">Votre rôle</label>
          <select id="role" name="role" class="form-select" required>
            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Sélectionnez votre rôle</option>
            <option value="etudiant"   {{ old('role')=='etudiant'   ? 'selected' : '' }}>Étudiant</option>
            <option value="tuteur"     {{ old('role')=='tuteur'     ? 'selected' : '' }}>Tuteur / Professeur</option>
            <option value="entreprise" {{ old('role')=='entreprise' ? 'selected' : '' }}>Entreprise</option>
            <option value="jury"       {{ old('role')=='jury'       ? 'selected' : '' }}>Jury</option>
            <option value="admin"      {{ old('role')=='admin'      ? 'selected' : '' }}>Administrateur</option>
          </select>
        </div>

        <button type="submit" class="btn-connexion">Se connecter</button>

        <div class="separateur">authentification sécurisée à deux facteurs</div>

        <p style="font-size:12px;color:var(--gris);text-align:center;">
          Un code de vérification vous sera envoyé par email après cette étape.
        </p>

        <p style="font-size:13px;color:var(--gris);text-align:center;margin-top:20px;">
          Pas encore de compte ?
          <a href="{{ route('register') }}" style="color:var(--bleu-clair);text-decoration:none;font-weight:500;">S'inscrire</a>
        </p>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Placeholder email dynamique selon le rôle
  document.getElementById('role').addEventListener('change', function () {
    const placeholders = {
      etudiant:   'prenom.nom@etu.cyu.fr',
      tuteur:     'prenom.nom@etu.cyu.fr',
      jury:       'prenom.nom@etu.cyu.fr',
      admin:      'prenom.nom@etu.cyu.fr',
      entreprise: 'contact@entreprise.fr',
    };
    document.getElementById('email').placeholder = placeholders[this.value] || 'prenom.nom@etu.cyu.fr';
  });
</script>
</body>
</html>
