<?php
require_once '../../backend/connexion.php';

// Récupération des infos depuis l'URL (GET) ou le formulaire (POST)
$user_id = (int)($_GET['uid']   ?? $_POST['uid']   ?? 0);
$role    = trim($_GET['role']   ?? $_POST['role']   ?? '');
$email   = trim($_GET['email']  ?? $_POST['email']  ?? '');
$code_saisi = trim($_POST['code_2fa'] ?? '');

// Si le formulaire est soumis avec le code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $code_saisi) {

    if (!$user_id || !$role || strlen($code_saisi) !== 6) {
        header("Location: verify_2fa.php?uid=$user_id&role=" . urlencode($role) . "&email=" . urlencode($email) . "&erreur=code");
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT * FROM AUTH_CODE
        WHERE id_utilisateur = ?
        AND code = ?
        AND utilise = 0
        AND date_expiration > NOW()
    ");
    $stmt->execute([$user_id, $code_saisi]);
    $auth = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$auth) {
        header("Location: verify_2fa.php?uid=$user_id&role=" . urlencode($role) . "&email=" . urlencode($email) . "&erreur=code");
        exit;
    }

    $stmt = $pdo->prepare("UPDATE AUTH_CODE SET utilise = 1 WHERE id = ?");
    $stmt->execute([$auth['id']]);

    session_start();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role']    = $role;

    $redirections = [
        'etudiant'   => 'dashboard_etudiant.php',
        'entreprise' => 'dashboard_entreprise.php',
        'tuteur'     => 'dashboard_tuteur.php',
        'jury'       => 'dashboard_tuteur.php',
        'admin'      => 'dashboard_admin.php',
    ];

    header('Location: ' . ($redirections[$role] ?? 'login.html'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vérification — Plateforme Stages CY Tech</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <style>
    :root { --bleu: #1a3a5c; --bleu-clair: #2d6a9f; --bordure: #d1dce8; --texte: #1c1c1c; --gris: #6b7280; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DM Sans', sans-serif; background: #f0f4f8; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
    .carte { background: white; border-radius: 16px; padding: 48px 40px; width: 100%; max-width: 440px; }
    .logo-zone { display: flex; align-items: center; gap: 10px; margin-bottom: 36px; }
    .logo-carre { width: 36px; height: 36px; background: var(--bleu); border-radius: 7px; display: flex; align-items: center; justify-content: center; font-family: 'DM Serif Display', serif; font-size: 16px; color: white; }
    .logo-texte { font-size: 14px; font-weight: 600; color: var(--bleu); }
    .icone-email { width: 56px; height: 56px; background: #e8f0f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
    .icone-email svg { width: 26px; height: 26px; fill: none; stroke: var(--bleu-clair); stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
    h2 { font-family: 'DM Serif Display', serif; font-size: 24px; color: var(--texte); margin-bottom: 8px; }
    .sous-titre { font-size: 14px; color: var(--gris); line-height: 1.6; margin-bottom: 32px; }
    .sous-titre span { font-weight: 600; color: var(--bleu); }
    .code-inputs { display: flex; gap: 10px; justify-content: center; margin-bottom: 28px; }
    .code-input { width: 52px; height: 60px; border: 1.5px solid var(--bordure); border-radius: 10px; text-align: center; font-size: 22px; font-weight: 600; font-family: 'DM Sans', sans-serif; color: var(--texte); background: #fafcff; outline: none; transition: border-color 0.2s; }
    .code-input:focus { border-color: var(--bleu-clair); box-shadow: 0 0 0 3px rgba(45,106,159,0.12); }
    .code-input.rempli { border-color: var(--bleu); background: #e8f0f7; }
    .btn-valider { width: 100%; padding: 13px; background: var(--bleu); color: white; border: none; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 600; cursor: pointer; margin-bottom: 16px; }
    .btn-valider:hover { background: var(--bleu-clair); }
    .btn-valider:disabled { background: #9db5cc; cursor: not-allowed; }
    .lien-renvoi { text-align: center; font-size: 13px; color: var(--gris); }
    .lien-renvoi a { color: var(--bleu-clair); text-decoration: none; font-weight: 500; }
    .timer { text-align: center; font-size: 12px; color: var(--gris); margin-top: 12px; }
    .timer span { font-weight: 600; color: var(--bleu); }
    .retour { display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--gris); text-decoration: none; margin-top: 24px; }
    .retour:hover { color: var(--bleu); }
    .alerte-erreur { display: none; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #dc2626; margin-bottom: 20px; }
  </style>
</head>
<body>
<div class="carte">
  <div class="logo-zone">
    <div class="logo-carre">CY</div>
    <span class="logo-texte">CY Tech — Plateforme Stages</span>
  </div>
  <div class="icone-email">
    <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="3"/><polyline points="2,4 12,13 22,4"/></svg>
  </div>
  <h2>Vérification en deux étapes</h2>
  <p class="sous-titre">Un code à 6 chiffres a été envoyé à <span><?= htmlspecialchars($email) ?></span>. Entrez-le ci-dessous.</p>

  <div class="alerte-erreur" id="alerte-erreur">Code incorrect ou expiré.</div>

  <form method="POST">
    <input type="hidden" name="uid"   value="<?= $user_id ?>">
    <input type="hidden" name="role"  value="<?= htmlspecialchars($role) ?>">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
    <input type="hidden" name="code_2fa" id="code-complet">

    <div class="code-inputs">
      <input class="code-input" type="text" maxlength="1" inputmode="numeric" autocomplete="off">
      <input class="code-input" type="text" maxlength="1" inputmode="numeric" autocomplete="off">
      <input class="code-input" type="text" maxlength="1" inputmode="numeric" autocomplete="off">
      <input class="code-input" type="text" maxlength="1" inputmode="numeric" autocomplete="off">
      <input class="code-input" type="text" maxlength="1" inputmode="numeric" autocomplete="off">
      <input class="code-input" type="text" maxlength="1" inputmode="numeric" autocomplete="off">
    </div>

    <button type="submit" class="btn-valider" id="btn-valider" disabled>Vérifier le code</button>
  </form>

  <div class="lien-renvoi">Pas reçu ? <a href="login.html">Recommencer</a></div>
  <div class="timer">Code valide encore <span id="countdown">10:00</span></div>
  <a href="login.html" class="retour">← Retour à la connexion</a>
</div>

<script>
  <?php if (isset($_GET['erreur'])): ?>
  document.getElementById('alerte-erreur').style.display = 'block';
  <?php endif; ?>

  const inputs = document.querySelectorAll('.code-input');
  const btn = document.getElementById('btn-valider');

  inputs.forEach((input, i) => {
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value.length === 1) {
        this.classList.add('rempli');
        if (i < inputs.length - 1) inputs[i + 1].focus();
      } else {
        this.classList.remove('rempli');
      }
      const code = Array.from(inputs).map(x => x.value).join('');
      document.getElementById('code-complet').value = code;
      btn.disabled = code.length < 6;
    });

    input.addEventListener('keydown', function(e) {
      if (e.key === 'Backspace' && !this.value && i > 0) {
        inputs[i - 1].value = '';
        inputs[i - 1].classList.remove('rempli');
        inputs[i - 1].focus();
      }
    });
  });

  let secondes = 600;
  const countdown = document.getElementById('countdown');
  const interval = setInterval(() => {
    secondes--;
    const m = String(Math.floor(secondes / 60)).padStart(2, '0');
    const s = String(secondes % 60).padStart(2, '0');
    countdown.textContent = m + ':' + s;
    if (secondes <= 0) { clearInterval(interval); countdown.textContent = 'expiré'; btn.disabled = true; }
  }, 1000);
</script>
</body>
</html>
