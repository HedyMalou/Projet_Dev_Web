<?php
$title = 'Mes documents';
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
  <a href="<?= route('etudiant.documents') ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
    Documents
  </a>
  <a href="<?= route('etudiant.demande-formation') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Demande de formation
  </a>
  <a href="<?= route('etudiant.profil') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    Mon profil
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Mes documents</h1>
  <p>Déposez et consultez vos documents de stage.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>
<?php if (session('erreur')): ?>
  <div class="alerte-erreur"><?= e(session('erreur')) ?></div>
<?php endif; ?>

<?php if ($candidatures_valides->isNotEmpty()): ?>
<div class="section-card">
  <div class="section-titre">Déposer un document de restitution</div>
  <p style="color:var(--gris);font-size:13px;margin:0 0 12px;">Rapport, résumé, fiche d'évaluation, ou autre document lié à votre stage.</p>
  <form method="POST" action="<?= route('etudiant.documents.upload') ?>" enctype="multipart/form-data"
        style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:end;">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <div>
      <label class="form-label-sm">Candidature</label>
      <select name="id_candidature" class="form-select" required>
        <?php foreach ($candidatures_valides as $c): ?>
          <option value="<?= e($c->id) ?>"><?= e($c->offre->titre ?? '—') ?> — <?= e($c->offre->entreprise->nom ?? '') ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="form-label-sm">Type</label>
      <select name="type" class="form-select" required>
        <option value="rapport">Rapport</option>
        <option value="resume">Résumé</option>
        <option value="fiche_evaluation">Fiche d'évaluation</option>
        <option value="autre">Autre</option>
      </select>
    </div>
    <div>
      <label class="form-label-sm">Fichier (PDF/DOC, max 10 Mo)</label>
      <input type="file" name="fichier" class="form-control" accept=".pdf,.doc,.docx" required>
    </div>
    <div>
      <button type="submit" class="btn-valider">Déposer</button>
    </div>
  </form>
</div>
<?php endif; ?>

<?php
  $labels = ['cv' => 'CV', 'lettre_motivation' => 'Lettre de motivation', 'rapport' => 'Rapport', 'resume' => 'Résumé', 'fiche_evaluation' => 'Fiche d\'évaluation', 'convention' => 'Convention', 'autre' => 'Autre'];
  $candidatures_avec_docs = $candidatures->filter(fn($c) => $c->documents->isNotEmpty());
?>

<?php if ($candidatures_avec_docs->isEmpty()): ?>
  <div class="section-card">
    <div class="vide">Aucun document déposé pour le moment.</div>
  </div>
<?php else: ?>
  <?php foreach ($candidatures_avec_docs as $c): ?>
  <div class="section-card">
    <div class="section-titre" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
      <span>
        <?= e($c->offre->titre ?? '—') ?>
        <span style="color:var(--gris);font-weight:400;"> — <?= e($c->offre->entreprise->nom ?? '—') ?></span>
      </span>
      <span style="font-size:12px;color:var(--gris);font-weight:400;">
        Candidature du <?= e(\Carbon\Carbon::parse($c->date_candidature)->format('d/m/Y')) ?>
      </span>
    </div>
    <table>
      <thead>
        <tr>
          <th>Type</th>
          <th>Fichier</th>
          <th>Déposé le</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($c->documents as $doc): ?>
        <tr>
          <td><?= e($labels[$doc->type] ?? $doc->type) ?></td>
          <td><?= e($doc->nom_original ?? basename($doc->chemin_fichier)) ?></td>
          <td><?= e(\Carbon\Carbon::parse($doc->date_depot)->format('d/m/Y')) ?></td>
          <td>
            <a href="<?= route('etudiant.documents.telecharger', $doc->id) ?>" class="btn-action">Télécharger</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endforeach; ?>
<?php endif; ?>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
