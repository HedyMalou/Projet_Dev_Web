<?php
$title = 'Gestion des offres';
$role_label = 'Administrateur';

ob_start(); ?>
  <a href="<?= route('admin.dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="<?= route('admin.offres') ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Offres
  </a>
  <a href="<?= route('admin.encadrement') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    Encadrement
  </a>
  <a href="<?= route('admin.archivage') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
    Archivage
  </a>
  <a href="<?= route('admin.demandes-formation') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Demandes formation
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Gestion des offres</h1>
  <p>Toutes les offres de stage publiées sur la plateforme.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>

<div class="section-card">
  <?php if ($offres->isEmpty()): ?>
    <div class="vide">Aucune offre publiée pour le moment.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Entreprise</th>
          <th>Lieu</th>
          <th>Durée</th>
          <th>Candidatures</th>
          <th>Publié le</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($offres as $offre): ?>
        <tr>
          <td style="font-weight:500;"><?= e($offre->titre) ?></td>
          <td><?= e($offre->entreprise->nom ?? '—') ?></td>
          <td><?= e($offre->lieu) ?></td>
          <td><?= e($offre->duree) ?></td>
          <td><?= e($offre->candidatures_count) ?></td>
          <td><?= e(\Carbon\Carbon::parse($offre->date_publication)->format('d/m/Y')) ?></td>
          <td>
            <form method="POST" action="<?= route('admin.supprimer-offre') ?>" style="display:inline;" onsubmit="return confirm('Supprimer cette offre ?')">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id_offre" value="<?= e($offre->id) ?>">
              <button type="submit" class="btn-supprimer">Supprimer</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
