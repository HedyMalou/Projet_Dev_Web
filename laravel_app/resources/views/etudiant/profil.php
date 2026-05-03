<?php
$title = 'Mon profil';
$role_label = 'Étudiant';

ob_start(); ?>
  <a href="<?= route('etudiant.dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="<?= route('etudiant.dossier') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Mon dossier
  </a>
  <a href="<?= route('etudiant.cahier') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
    Cahier de stage
  </a>
  <a href="<?= route('etudiant.documents') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
    Documents
  </a>
  <a href="<?= route('etudiant.demande-formation') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Demande de formation
  </a>
  <a href="<?= route('etudiant.profil') ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    Mon profil
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Mon profil</h1>
  <p>Modifiez vos informations personnelles.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>
<?php if ($errors->any()): ?>
  <div class="alerte-erreur"><?php foreach ($errors->all() as $err): ?><?= e($err) ?><br><?php endforeach; ?></div>
<?php endif; ?>

<div class="kpi-grid-2">
  <div class="kpi-card">
    <div class="kpi-label">Candidatures</div>
    <div class="kpi-valeur"><?= e($nb_cand) ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Documents</div>
    <div class="kpi-valeur"><?= e($nb_doc) ?></div>
  </div>
</div>

<div class="section-card">
  <div class="section-titre">Informations personnelles</div>
  <form method="POST" action="<?= route('etudiant.profil.update') ?>" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <div>
      <label class="form-label-sm">Prénom</label>
      <input type="text" class="form-control" value="<?= e($etudiant->utilisateur->prenom) ?>" disabled style="background:#f3f4f6;">
    </div>
    <div>
      <label class="form-label-sm">Nom</label>
      <input type="text" class="form-control" value="<?= e($etudiant->utilisateur->nom) ?>" disabled style="background:#f3f4f6;">
    </div>
    <div style="grid-column:1/-1;">
      <label class="form-label-sm">Numéro étudiant</label>
      <input type="text" class="form-control" value="<?= e($etudiant->numero_etudiant) ?>" disabled style="background:#f3f4f6;">
    </div>
    <div style="grid-column:1/-1;">
      <label class="form-label-sm">Email</label>
      <input type="email" name="email" class="form-control" value="<?= e(old('email', $etudiant->utilisateur->email)) ?>" required>
    </div>
    <div>
      <label class="form-label-sm">Filière</label>
      <input type="text" name="filiere" class="form-control" value="<?= e(old('filiere', $etudiant->filiere)) ?>" required>
    </div>
    <div>
      <label class="form-label-sm">Promotion</label>
      <input type="text" name="promotion" class="form-control" value="<?= e(old('promotion', $etudiant->promotion)) ?>" required>
    </div>
    <div>
      <label class="form-label-sm">Nouveau mot de passe (optionnel)</label>
      <input type="password" name="mot_de_passe" class="form-control" autocomplete="new-password">
    </div>
    <div>
      <label class="form-label-sm">Confirmer mot de passe</label>
      <input type="password" name="mot_de_passe_confirmation" class="form-control" autocomplete="new-password">
    </div>
    <div style="grid-column:1/-1;">
      <button type="submit" class="btn-valider">Enregistrer les modifications</button>
    </div>
  </form>
</div>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
