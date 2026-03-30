<?php $__env->startSection('title', 'Vérification A2F'); ?>

<?php $__env->startSection('content'); ?>
  <div class="auth-titre">Vérification en 2 étapes</div>
  <div class="auth-sous-titre">Entrez le code à 6 chiffres envoyé sur votre messagerie.</div>

  
  <?php if(session('a2f_code_debug')): ?>
    <div class="debug-box">
      <strong>Mode développement</strong><br>
      Votre code A2F : <span style="font-size:20px;font-weight:700;letter-spacing:4px"><?php echo e(session('a2f_code_debug')); ?></span><br>
      <small>(Ce bloc sera supprimé en production)</small>
    </div>
  <?php endif; ?>

  <?php if($errors->any()): ?>
    <div class="alerte-erreur"><?php echo e($errors->first()); ?></div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(url('/a2f')); ?>">
    <?php echo csrf_field(); ?>

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
    Mauvais compte ? <a href="<?php echo e(url('/login')); ?>">Retour à la connexion</a>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/cytech/Projet_Dev_Web/laravel_app/resources/views/auth/a2f.blade.php ENDPATH**/ ?>