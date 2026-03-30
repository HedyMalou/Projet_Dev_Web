<?php $__env->startSection('title', 'Mon dossier'); ?>
<?php $__env->startSection('role-label', 'Étudiant'); ?>

<?php $__env->startSection('nav'); ?>
  <a href="<?php echo e(route('etudiant.dashboard')); ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="<?php echo e(route('etudiant.dossier')); ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Mon dossier
  </a>
  <a href="<?php echo e(route('etudiant.documents')); ?>" class="nav-item">
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
  <h1>Mon dossier</h1>
  <p>Suivez vos candidatures et conventions.</p>
</div>

<?php if(session('succes')): ?>
  <div class="alerte-succes"><?php echo e(session('succes')); ?></div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Mes candidatures</div>
  <?php if($candidatures->isEmpty()): ?>
    <div class="vide">Vous n'avez encore postulé à aucune offre.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Offre</th>
          <th>Entreprise</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Convention</th>
          <th>Documents</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $candidatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td style="font-weight:500;"><?php echo e($c->offre->titre ?? '—'); ?></td>
          <td><?php echo e($c->offre->entreprise->nom ?? '—'); ?></td>
          <td><?php echo e(\Carbon\Carbon::parse($c->date_candidature)->format('d/m/Y')); ?></td>
          <td>
            <?php if($c->statut === 'en_attente'): ?> <span class="badge-attente">En attente</span>
            <?php elseif($c->statut === 'validee'): ?>  <span class="badge-validee">Validée</span>
            <?php elseif($c->statut === 'refusee'): ?>  <span class="badge-refusee">Refusée</span>
            <?php else: ?>                              <span class="badge-archive">Archivée</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($c->convention): ?>
              <span class="badge-validee">Signée</span>
            <?php else: ?>
              <span style="color:var(--gris);font-size:12px;">—</span>
            <?php endif; ?>
          </td>
          <td>
            <?php echo e(count($documents_par_cand[$c->id] ?? [])); ?> fichier(s)
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php if($commentaires->isNotEmpty()): ?>
<div class="section-card">
  <div class="section-titre">Commentaires reçus</div>
  <?php $__currentLoopData = $commentaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $com): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div style="border-bottom:0.5px solid var(--bordure);padding:12px 0;last-child:border:none;">
    <div style="font-size:12px;color:var(--gris);margin-bottom:4px;">
      <?php echo e($com->utilisateur->prenom ?? ''); ?> <?php echo e($com->utilisateur->nom ?? ''); ?> — <?php echo e(\Carbon\Carbon::parse($com->date)->format('d/m/Y H:i')); ?>

    </div>
    <div style="font-size:14px;"><?php echo e($com->contenu); ?></div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/cytech/Projet_Dev_Web/laravel_app/resources/views/etudiant/mon_dossier.blade.php ENDPATH**/ ?>