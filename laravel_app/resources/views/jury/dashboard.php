<?php
$title = 'Tableau de bord jury';
$role_label = 'Jury';

ob_start(); ?>
  <a href="<?= route('jury.dashboard') ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
    Évaluation stages
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Évaluation des stages</h1>
  <p>Notez et commentez les stages validés.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Stages en cours</div>
  <?php if ($candidatures->isEmpty()): ?>
    <div class="vide">Aucun stage validé à évaluer pour le moment.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Entreprise</th>
          <th>Note actuelle</th>
          <th>Attribuer une note</th>
          <th>Commentaire</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($candidatures as $c): ?>
        <tr>
          <td>
            <?= e($c->etudiant->utilisateur->prenom ?? '') ?> <?= e($c->etudiant->utilisateur->nom ?? '') ?>
            <br>
            <a href="<?= route('jury.cahier', $c->id) ?>" style="font-size:12px;color:var(--bleu-clair);">Voir cahier de stage</a>
          </td>
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
            <form method="POST" action="<?= route('jury.noter') ?>" style="display:flex;gap:6px;">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id_candidature" value="<?= e($c->id) ?>">
              <input type="number" name="note" class="form-control" style="width:70px;" min="0" max="20" step="0.5" placeholder="0–20" required>
              <button type="submit" class="btn-action">Valider</button>
            </form>
          </td>
          <td>
            <form method="POST" action="<?= route('jury.commenter') ?>" style="display:flex;gap:6px;">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id_candidature" value="<?= e($c->id) ?>">
              <input type="text" name="contenu" class="form-control" style="width:160px;" placeholder="Votre commentaire…" required>
              <button type="submit" class="btn-action">Envoyer</button>
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
