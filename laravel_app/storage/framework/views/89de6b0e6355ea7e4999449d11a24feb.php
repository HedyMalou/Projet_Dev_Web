<?php $__env->startSection('title', 'Mes documents'); ?>
<?php $__env->startSection('role-label', 'Étudiant'); ?>

<?php $__env->startSection('nav'); ?>
  <a href="<?php echo e(route('etudiant.dashboard')); ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="<?php echo e(route('etudiant.dossier')); ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Mon dossier
  </a>
  <a href="<?php echo e(route('etudiant.documents')); ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
    Documents
  </a>
  <a href="<?php echo e(route('etudiant.profil')); ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    Mon profil
  </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
  <h1>Mes documents</h1>
  <p>Déposez et consultez vos documents de stage.</p>
</div>

<?php if(session('succes')): ?>
  <div class="alerte-succes"><?php echo e(session('succes')); ?></div>
<?php endif; ?>
<?php if(session('erreur')): ?>
  <div class="alerte-erreur"><?php echo e(session('erreur')); ?></div>
<?php endif; ?>


<?php if($candidatures_valides->isNotEmpty()): ?>
<div class="section-card">
  <div class="section-titre">Déposer un document</div>
  <form method="POST" action="<?php echo e(route('etudiant.documents.upload')); ?>" enctype="multipart/form-data"
        style="display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end;">
    <?php echo csrf_field(); ?>
    <div>
      <label class="form-label-sm">Candidature</label>
      <select name="id_candidature" class="form-select" required>
        <?php $__currentLoopData = $candidatures_valides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($c->id); ?>"><?php echo e($c->offre->titre ?? '—'); ?> — <?php echo e($c->offre->entreprise->nom ?? ''); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="form-label-sm">Fichier (PDF, max 5 Mo)</label>
      <input type="file" name="fichier" class="form-control" accept=".pdf,.doc,.docx" required>
    </div>
    <div>
      <button type="submit" class="btn-valider">Déposer</button>
    </div>
  </form>
</div>
<?php endif; ?>


<div class="section-card">
  <div class="section-titre">Documents déposés</div>
  <?php if($documents->isEmpty()): ?>
    <div class="vide">Aucun document déposé pour le moment.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Stage</th>
          <th>Déposé le</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($doc->nom_fichier); ?></td>
          <td><?php echo e($doc->candidature->offre->titre ?? '—'); ?></td>
          <td><?php echo e(\Carbon\Carbon::parse($doc->date_depot)->format('d/m/Y')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/cytech/Projet_Dev_Web/laravel_app/resources/views/etudiant/documents.blade.php ENDPATH**/ ?>