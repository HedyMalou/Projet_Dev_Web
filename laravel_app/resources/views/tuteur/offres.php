<?php
$title = 'Offres de stage';
$role_label = 'Tuteur';

ob_start(); ?>
  <a href="<?= route('tuteur.dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Mes étudiants
  </a>
  <a href="<?= route('tuteur.offres') ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
    Offres de stage
  </a>
  <a href="<?= route('tuteur.demandes') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Demandes d'encadrement
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Offres de stage</h1>
  <p>Consultez les offres publiées par les entreprises partenaires.</p>
</div>

<div class="section-card">
  <form method="GET" action="<?= route('tuteur.offres') ?>" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Mots-clés</label>
      <input type="text" name="q" class="form-control" value="<?= e($q) ?>" placeholder="titre, compétences..." style="min-width:220px;">
    </div>
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Durée</label>
      <input type="text" name="duree" class="form-control" value="<?= e($duree) ?>" placeholder="3 mois" style="min-width:140px;">
    </div>
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Lieu</label>
      <input type="text" name="lieu" class="form-control" value="<?= e($lieu) ?>" placeholder="Paris" style="min-width:140px;">
    </div>
    <button type="submit" class="btn-action">Filtrer</button>
  </form>
</div>

<div class="section-card">
  <div class="section-titre">Offres disponibles (<?= $offres->count() ?>)</div>
  <?php if ($offres->isEmpty()): ?>
    <div class="vide">Aucune offre trouvée.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Entreprise</th>
          <th>Lieu</th>
          <th>Durée</th>
          <th>Compétences</th>
          <th>Publié le</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($offres as $offre): ?>
        <tr>
          <td style="font-weight:500;"><?= e($offre->titre) ?></td>
          <td><?= e($offre->entreprise->nom ?? '—') ?></td>
          <td><?= e($offre->lieu) ?></td>
          <td><?= e($offre->duree) ?></td>
          <td style="font-size:13px;color:var(--gris);"><?= e($offre->competences) ?></td>
          <td><?= e(\Carbon\Carbon::parse($offre->date_publication)->format('d/m/Y')) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
