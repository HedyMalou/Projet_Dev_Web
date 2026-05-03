<?php
$title = 'Archivage';
$role_label = 'Administrateur';

ob_start(); ?>
  <a href="<?= route('admin.dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="<?= route('admin.offres') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Offres
  </a>
  <a href="<?= route('admin.encadrement') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    Encadrement
  </a>
  <a href="<?= route('admin.archivage') ?>" class="nav-item active">
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
  <h1>Archivage des stages</h1>
  <p>Archivez les dossiers de stages terminés.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Stages validés — à archiver</div>
  <?php if ($stages_valides->isEmpty()): ?>
    <div class="vide">Aucun stage validé en attente d'archivage.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Entreprise</th>
          <th>Tuteur</th>
          <th>Note</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($stages_valides as $c): ?>
        <tr>
          <td><?= e($c->etudiant->utilisateur->prenom ?? '') ?> <?= e($c->etudiant->utilisateur->nom ?? '') ?></td>
          <td><?= e($c->offre->titre ?? '—') ?></td>
          <td><?= e($c->offre->entreprise->nom ?? '—') ?></td>
          <td>
            <?php if ($c->suivi && $c->suivi->tuteur): ?>
              <?= e($c->suivi->tuteur->utilisateur->prenom ?? '') ?> <?= e($c->suivi->tuteur->utilisateur->nom ?? '') ?>
            <?php else: ?>
              <span style="color:var(--gris);">Non affecté</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($c->suivi && $c->suivi->note_finale !== null): ?>
              <span class="badge-note"><?= e($c->suivi->note_finale) ?>/20</span>
            <?php else: ?>
              <span style="color:var(--gris);font-size:12px;">—</span>
            <?php endif; ?>
          </td>
          <td>
            <form method="POST" action="<?= route('admin.archiver') ?>" style="display:inline;">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id_candidature" value="<?= e($c->id) ?>">
              <button type="submit" class="btn-action">Archiver</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="section-card">
  <div class="section-titre">Dossiers archivés</div>
  <?php if ($archives->isEmpty()): ?>
    <div class="vide">Aucun dossier archivé.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Entreprise</th>
          <th>Note finale</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($archives as $c): ?>
        <tr>
          <td><?= e($c->etudiant->utilisateur->prenom ?? '') ?> <?= e($c->etudiant->utilisateur->nom ?? '') ?></td>
          <td><?= e($c->offre->titre ?? '—') ?></td>
          <td><?= e($c->offre->entreprise->nom ?? '—') ?></td>
          <td>
            <?php if ($c->suivi && $c->suivi->note_finale !== null): ?>
              <span class="badge-note"><?= e($c->suivi->note_finale) ?>/20</span>
            <?php else: ?>
              <span style="color:var(--gris);font-size:12px;">—</span>
            <?php endif; ?>
          </td>
          <td>
            <form method="POST" action="<?= route('admin.desarchiver', $c->id) ?>" style="display:inline;"
                  onsubmit="return confirm('Réactiver ce dossier ? Les informations seront à nouveau disponibles pour l\'étudiant et l\'entreprise.')">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <button type="submit" class="btn-action">Réactiver</button>
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
