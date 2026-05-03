<?php
$title = 'Tableau de bord tuteur';
$role_label = 'Tuteur';

ob_start(); ?>
  <a href="<?= route('tuteur.dashboard') ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Mes étudiants
  </a>
  <a href="<?= route('tuteur.offres') ?>" class="nav-item">
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
  <h1>Bonjour, <?= e(session('prenom')) ?></h1>
  <p>Suivez et évaluez vos étudiants en stage.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>

<div class="kpi-grid-2">
  <div class="kpi-card">
    <div class="kpi-label">Étudiants suivis</div>
    <div class="kpi-valeur"><?= e($nb_etudiants) ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Conventions en attente</div>
    <div class="kpi-valeur"><?= e($nb_conv_attente) ?></div>
  </div>
</div>

<?php
  $conv_a_valider = $etudiants->filter(function($s) {
      $conv = $s->candidature->convention ?? null;
      return $conv && $conv->statut_etudiant === 'signe' && $conv->statut_tuteur === 'en_attente';
  });
?>
<?php if ($conv_a_valider->isNotEmpty()): ?>
<div class="section-card">
  <div class="section-titre">
    Conventions à valider
    <span style="background:#f59e0b;color:white;font-size:12px;padding:2px 8px;border-radius:10px;margin-left:8px;"><?= e($conv_a_valider->count()) ?></span>
  </div>
  <table>
    <thead>
      <tr>
        <th>Étudiant</th>
        <th>Stage</th>
        <th>Fichier</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($conv_a_valider as $s): ?>
      <?php $c = $s->candidature; $conv = $c->convention; ?>
      <tr>
        <td><?= e($c->etudiant->utilisateur->prenom ?? '') ?> <?= e($c->etudiant->utilisateur->nom ?? '') ?></td>
        <td><?= e($c->offre->titre ?? '—') ?></td>
        <td><?= e($conv->nom_original ?? basename($conv->chemin_fichier)) ?></td>
        <td style="display:flex;gap:8px;">
          <a href="<?= route('tuteur.convention.telecharger', $conv->id) ?>" class="btn-action">Télécharger</a>
          <form method="POST" action="<?= route('tuteur.convention.valider', $conv->id) ?>" style="display:inline;">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
            <button type="submit" class="btn-valider">Valider</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Étudiants en stage</div>
  <?php if ($etudiants->isEmpty()): ?>
    <div class="vide">Aucun étudiant ne vous a encore été affecté.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Entreprise</th>
          <th>Cahier</th>
          <th>Note finale</th>
          <th>Commenter</th>
          <th>Noter</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($etudiants as $suivi): ?>
        <?php $c = $suivi->candidature; ?>
        <tr>
          <td><?= e($c->etudiant->utilisateur->prenom ?? '') ?> <?= e($c->etudiant->utilisateur->nom ?? '') ?></td>
          <td><?= e($c->offre->titre ?? '—') ?></td>
          <td><?= e($c->offre->entreprise->nom ?? '—') ?></td>
          <td><a href="<?= route('tuteur.cahier', $c->id) ?>" class="btn-action">Voir</a></td>
          <td>
            <?php if ($suivi->note_finale !== null): ?>
              <span class="badge-note"><?= e($suivi->note_finale) ?>/20</span>
            <?php else: ?>
              <span style="color:var(--gris);font-size:12px;">Non noté</span>
            <?php endif; ?>
          </td>
          <td>
            <form method="POST" action="<?= route('tuteur.commenter') ?>" style="display:flex;gap:6px;">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id_candidature" value="<?= e($c->id) ?>">
              <input type="text" name="contenu" class="form-control" style="width:160px;" placeholder="Votre commentaire…" required>
              <button type="submit" class="btn-action">Envoyer</button>
            </form>
          </td>
          <td>
            <form method="POST" action="<?= route('tuteur.noter') ?>" style="display:flex;gap:6px;">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id_candidature" value="<?= e($c->id) ?>">
              <input type="number" name="note" class="form-control" style="width:70px;" min="0" max="20" step="0.5" placeholder="0–20" required>
              <button type="submit" class="btn-action">Enregistrer</button>
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
