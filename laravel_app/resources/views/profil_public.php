<?php
$title = 'Profil — ' . ($user->prenom . ' ' . $user->nom);
$role_label = ucfirst(session('role') ?? '');

ob_start(); ?>
  <a href="<?= route('dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1><?= e($user->prenom) ?> <?= e($user->nom) ?></h1>
  <p>
    <span class="badge-role <?= $user->role === 'admin' ? 'badge-admin' : '' ?>"><?= e(ucfirst($user->role)) ?></span>
  </p>
</div>

<div class="section-card">
  <div class="section-titre">Informations</div>
  <table>
    <tbody>
      <tr><td style="color:var(--gris);width:200px;">Nom complet</td><td><?= e($user->prenom) ?> <?= e($user->nom) ?></td></tr>
      <tr><td style="color:var(--gris);">Email</td><td><?= e($user->email) ?></td></tr>
      <tr><td style="color:var(--gris);">Inscrit le</td><td><?= e(\Carbon\Carbon::parse($user->created_at)->format('d/m/Y')) ?></td></tr>

      <?php if ($user->role === 'etudiant' && $user->etudiant): ?>
        <tr><td style="color:var(--gris);">Numéro étudiant</td><td><?= e($user->etudiant->numero_etudiant) ?></td></tr>
        <tr><td style="color:var(--gris);">Filière</td><td><?= e($user->etudiant->filiere) ?></td></tr>
        <tr><td style="color:var(--gris);">Promotion</td><td><?= e($user->etudiant->promotion) ?></td></tr>
      <?php elseif ($user->role === 'tuteur' && $user->tuteur): ?>
        <tr><td style="color:var(--gris);">Département</td><td><?= e($user->tuteur->departement) ?></td></tr>
      <?php elseif ($user->role === 'jury' && $user->jury): ?>
        <tr><td style="color:var(--gris);">Spécialité</td><td><?= e($user->jury->specialite) ?></td></tr>
      <?php elseif ($user->role === 'entreprise' && $user->entreprise): ?>
        <tr><td style="color:var(--gris);">Nom de l'entreprise</td><td><?= e($user->entreprise->nom_entreprise) ?></td></tr>
        <?php if ($user->entreprise->secteur): ?>
          <tr><td style="color:var(--gris);">Secteur</td><td><?= e($user->entreprise->secteur) ?></td></tr>
        <?php endif; ?>
        <?php if ($user->entreprise->adresse): ?>
          <tr><td style="color:var(--gris);">Adresse</td><td><?= e($user->entreprise->adresse) ?></td></tr>
        <?php endif; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $content = ob_get_clean();

require __DIR__ . '/layouts/app.php';
