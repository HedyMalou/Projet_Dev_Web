<?php
$title = 'Vérification A2F';

ob_start(); ?>
  <div class="auth-titre">Vérification en 2 étapes</div>
  <div class="auth-sous-titre">Entrez le code à 6 chiffres envoyé sur votre messagerie.</div>

  <?php if (session('a2f_code_debug')): ?>
    <div class="debug-box">
      <strong>Mode développement</strong><br>
      Votre code A2F : <span style="font-size:20px;font-weight:700;letter-spacing:4px"><?= e(session('a2f_code_debug')) ?></span>
    </div>
  <?php endif; ?>

  <?php if ($errors->any()): ?>
    <div class="alerte-erreur"><?= e($errors->first()) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= url('/a2f') ?>">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">

    <div class="mb-3">
      <label class="form-label">Code de vérification</label>
      <input type="text" name="code" class="form-control"
             maxlength="6" placeholder="123456"
             style="font-size:22px;letter-spacing:6px;text-align:center;font-weight:600"
             required autofocus autocomplete="one-time-code">
    </div>

    <button type="submit" class="btn-auth">Valider</button>
  </form>

  <hr class="separateur">

  <div class="auth-link">
    Mauvais compte ? <a href="<?= url('/login') ?>">Retour à la connexion</a>
  </div>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/auth.php';
