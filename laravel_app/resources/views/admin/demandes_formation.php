<?php
$title = 'Demandes de formation';
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
  <a href="<?= route('admin.archivage') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
    Archivage
  </a>
  <a href="<?= route('admin.demandes-formation') ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Demandes formation
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Demandes d'ajout de formation</h1>
  <p>Validez ou refusez les demandes soumises par les étudiants.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>
<?php if ($errors->any()): ?>
  <div class="alerte-erreur"><?= e($errors->first()) ?></div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Toutes les demandes (<?= $demandes->count() ?>)</div>
  <?php if ($demandes->isEmpty()): ?>
    <div class="vide">Aucune demande pour le moment.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Formation demandée</th>
          <th>Description</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($demandes as $d): ?>
        <tr>
          <td>
            <?= e($d->etudiant->utilisateur->prenom ?? '') ?> <?= e($d->etudiant->utilisateur->nom ?? '') ?>
            <br><span style="font-size:11px;color:var(--gris);"><?= e($d->etudiant->numero_etudiant ?? '') ?></span>
          </td>
          <td style="font-weight:500;"><?= e($d->nom_formation) ?></td>
          <td style="font-size:13px;color:var(--gris);max-width:280px;"><?= e($d->description ?? '—') ?></td>
          <td><?= e(\Carbon\Carbon::parse($d->date_demande)->format('d/m/Y H:i')) ?></td>
          <td>
            <?php if ($d->statut === 'en_attente'): ?>
              <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:10px;font-size:12px;">En attente</span>
            <?php elseif ($d->statut === 'validee'): ?>
              <span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:10px;font-size:12px;">Validée</span>
            <?php else: ?>
              <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:10px;font-size:12px;">Refusée</span>
            <?php endif; ?>
            <?php if ($d->reponse_admin): ?>
              <div style="font-size:11px;color:var(--gris);margin-top:4px;font-style:italic;">"<?= e($d->reponse_admin) ?>"</div>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($d->statut === 'en_attente'): ?>
              <div style="display:flex;flex-direction:column;gap:6px;">
                <form method="POST" action="<?= route('admin.demandes-formation.valider', $d->id) ?>" style="display:flex;gap:6px;">
                  <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                  <input type="text" name="reponse" class="form-control" style="width:140px;font-size:12px;" placeholder="Réponse (optionnel)">
                  <button type="submit" class="btn-valider" style="font-size:12px;">Valider</button>
                </form>
                <form method="POST" action="<?= route('admin.demandes-formation.refuser', $d->id) ?>" style="display:flex;gap:6px;">
                  <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                  <input type="text" name="reponse" class="form-control" style="width:140px;font-size:12px;" placeholder="Motif du refus" required>
                  <button type="submit" class="btn-action" style="background:#dc2626;font-size:12px;">Refuser</button>
                </form>
              </div>
            <?php else: ?>
              <span style="color:var(--gris);font-size:12px;">Traitée le <?= e(\Carbon\Carbon::parse($d->date_traitement)->format('d/m/Y')) ?></span>
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
