<?php
$title = 'Cahier de stage';
$role_label = 'Tuteur';

ob_start(); ?>
  <a href="<?= route('tuteur.dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Mes étudiants
  </a>
  <a href="<?= route('tuteur.demandes') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Demandes d'encadrement
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Cahier de stage</h1>
  <p>
    <?= e($candidature->etudiant->utilisateur->prenom ?? '') ?> <?= e($candidature->etudiant->utilisateur->nom ?? '') ?>
    — <?= e($candidature->offre->titre ?? '—') ?>
    chez <?= e($candidature->offre->entreprise->nom ?? '') ?>
  </p>
  <p><a href="<?= route('tuteur.dashboard') ?>" style="color:var(--bleu-clair);">← Retour à mes étudiants</a></p>
</div>

<div class="section-card">
  <?php if ($entrees->isEmpty()): ?>
    <div class="vide">L'étudiant n'a pas encore renseigné son cahier de stage.</div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:14px;">
      <?php foreach ($entrees as $e): ?>
        <div style="border-left:3px solid var(--bleu-clair);padding:10px 16px;background:#fafcff;">
          <div style="font-size:12px;color:var(--gris);margin-bottom:6px;font-weight:500;">
            <?= e(\Carbon\Carbon::parse($e->date_jour)->isoFormat('dddd D MMMM YYYY')) ?>
          </div>
          <div style="font-size:13px;white-space:pre-wrap;"><?= e($e->contenu) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
