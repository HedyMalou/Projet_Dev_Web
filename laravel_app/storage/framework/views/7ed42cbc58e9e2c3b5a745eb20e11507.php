<?php $__env->startSection('title', 'Mon tableau de bord'); ?>
<?php $__env->startSection('role-label', 'Étudiant'); ?>

<?php $__env->startSection('nav'); ?>
  <a href="<?php echo e(route('etudiant.dashboard')); ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="<?php echo e(route('etudiant.dossier')); ?>" class="nav-item">
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
  <h1>Bonjour, <?php echo e(session('prenom')); ?> 👋</h1>
  <p>Trouvez et postulez à des offres de stage.</p>
</div>

<?php if(session('succes')): ?>
  <div class="alerte-succes"><?php echo e(session('succes')); ?></div>
<?php endif; ?>
<?php if(session('erreur')): ?>
  <div class="alerte-erreur"><?php echo e(session('erreur')); ?></div>
<?php endif; ?>

<div class="kpi-grid-2">
  <div class="kpi-card">
    <div class="kpi-label">Candidatures envoyées</div>
    <div class="kpi-valeur"><?php echo e($nb_candidatures); ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Documents déposés</div>
    <div class="kpi-valeur"><?php echo e($nb_documents); ?></div>
  </div>
</div>


<div class="section-card">
  <div class="section-titre">Rechercher des offres</div>
  <form method="GET" action="<?php echo e(route('etudiant.dashboard')); ?>" style="display:grid;grid-template-columns:1fr 160px 160px auto;gap:12px;align-items:end;">
    <div>
      <label class="form-label-sm">Mot-clé</label>
      <input type="text" name="q" value="<?php echo e($q); ?>" class="form-control" placeholder="Titre, compétence…">
    </div>
    <div>
      <label class="form-label-sm">Durée</label>
      <select name="duree" class="form-select">
        <option value="">Toutes</option>
        <?php $__currentLoopData = ['1 mois','2 mois','3 mois','4 mois','5 mois','6 mois']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($d); ?>" <?php echo e($duree==$d?'selected':''); ?>><?php echo e($d); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label class="form-label-sm">Lieu</label>
      <input type="text" name="lieu" value="<?php echo e($lieu); ?>" class="form-control" placeholder="Paris…">
    </div>
    <div>
      <button type="submit" class="btn-valider" style="white-space:nowrap;">Rechercher</button>
    </div>
  </form>
</div>


<div class="section-card">
  <div class="section-titre">Offres disponibles</div>
  <?php if($offres->isEmpty()): ?>
    <div class="vide">Aucune offre trouvée.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Entreprise</th>
          <th>Lieu</th>
          <th>Durée</th>
          <th>Publié le</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $offres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td style="font-weight:500;"><?php echo e($offre->titre); ?></td>
          <td><?php echo e($offre->entreprise->nom ?? '—'); ?></td>
          <td><?php echo e($offre->lieu); ?></td>
          <td><?php echo e($offre->duree); ?></td>
          <td><?php echo e(\Carbon\Carbon::parse($offre->date_publication)->format('d/m/Y')); ?></td>
          <td>
            <?php if(in_array($offre->id, $offres_postulees)): ?>
              <span class="badge-validee">Postulé</span>
            <?php else: ?>
              <form method="POST" action="<?php echo e(route('etudiant.postuler')); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id_offre" value="<?php echo e($offre->id); ?>">
                <button type="submit" class="btn-action">Postuler</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/cytech/Projet_Dev_Web/laravel_app/resources/views/etudiant/dashboard.blade.php ENDPATH**/ ?>