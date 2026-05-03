<?php
$title = 'Cahier de stage';
$role_label = 'Jury';

ob_start(); ?>
  <a href="<?= route('jury.dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
    Évaluation stages
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
  <p><a href="<?= route('jury.dashboard') ?>" style="color:var(--bleu-clair);">← Retour à l'évaluation</a></p>
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
