<?php
$title = 'Mes demandes d\'encadrement';
$role_label = 'Tuteur';

ob_start(); ?>
  <a href="<?= route('tuteur.dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Mes étudiants
  </a>
  <a href="<?= route('tuteur.offres') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
    Offres de stage
  </a>
  <a href="<?= route('tuteur.demandes') ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Demandes d'encadrement
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Demandes d'encadrement</h1>
  <p>Demandez à encadrer un étudiant. L'administrateur validera votre demande.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>
<?php if (session('erreur')): ?>
  <div class="alerte-erreur"><?= e(session('erreur')) ?></div>
<?php endif; ?>

<?php if ($errors->any()): ?>
  <div class="alerte-erreur"><?= e($errors->first()) ?></div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Nouvelle demande</div>
  <form method="POST" action="<?= route('tuteur.soumettre-demande') ?>" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Nom de l'étudiant</label>
      <input type="text" name="nom_etudiant" class="form-control" style="min-width:180px;" required value="<?= e(old('nom_etudiant')) ?>">
    </div>
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Prénom</label>
      <input type="text" name="prenom_etudiant" class="form-control" style="min-width:180px;" required value="<?= e(old('prenom_etudiant')) ?>">
    </div>
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Numéro étudiant</label>
      <input type="text" name="numero_etudiant" class="form-control" style="min-width:160px;" required value="<?= e(old('numero_etudiant')) ?>">
    </div>
    <button type="submit" class="btn-action">Envoyer la demande</button>
  </form>
</div>

<div class="section-card">
  <div class="section-titre">Mes demandes</div>
  <?php if ($demandes->isEmpty()): ?>
    <div class="vide">Vous n'avez pas encore fait de demande.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Étudiant demandé</th>
          <th>N° étudiant</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Détails</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($demandes as $d): ?>
        <tr>
          <td><?= e($d->prenom_etudiant) ?> <?= e($d->nom_etudiant) ?></td>
          <td><?= e($d->numero_etudiant) ?></td>
          <td><?= e(\Carbon\Carbon::parse($d->date_demande)->format('d/m/Y H:i')) ?></td>
          <td>
            <?php if ($d->statut === 'en_attente'): ?>
              <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:10px;font-size:12px;">En attente</span>
            <?php elseif ($d->statut === 'acceptee'): ?>
              <span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:10px;font-size:12px;">Acceptée</span>
            <?php else: ?>
              <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:10px;font-size:12px;">Refusée</span>
            <?php endif; ?>
          </td>
          <td style="font-size:13px;color:var(--gris);">
            <?php if ($d->statut === 'acceptee' && $d->candidature): ?>
              Stage : <?= e($d->candidature->offre->titre ?? '—') ?> — <?= e($d->candidature->offre->entreprise->nom ?? '—') ?>
            <?php elseif ($d->statut === 'refusee'): ?>
              Motif : <?= e($d->motif_refus ?? '—') ?>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
