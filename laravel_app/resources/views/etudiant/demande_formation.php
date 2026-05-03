<?php
$title = 'Demande d\'ajout de formation';
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
  <a href="<?= route('etudiant.demande-formation') ?>" class="nav-item active">
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
  <h1>Demande d'ajout de formation</h1>
  <p>Si votre filière n'apparaît pas dans la liste proposée, demandez son ajout à l'administrateur.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>
<?php if ($errors->any()): ?>
  <div class="alerte-erreur"><?= e($errors->first()) ?></div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Nouvelle demande</div>
  <form method="POST" action="<?= route('etudiant.demande-formation.soumettre') ?>" style="display:flex;flex-direction:column;gap:12px;max-width:600px;">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <div>
      <label class="form-label-sm">Nom de la formation</label>
      <input type="text" name="nom_formation" class="form-control" required value="<?= e(old('nom_formation')) ?>" placeholder="Ex: Cybersécurité, Data Science...">
    </div>
    <div>
      <label class="form-label-sm">Description (optionnel)</label>
      <textarea name="description" class="form-control" rows="3" placeholder="Précisez le contenu, le département, ou tout détail utile..."><?= e(old('description')) ?></textarea>
    </div>
    <div>
      <button type="submit" class="btn-valider">Envoyer la demande</button>
    </div>
  </form>
</div>

<div class="section-card">
  <div class="section-titre">Historique de mes demandes</div>
  <?php if ($demandes->isEmpty()): ?>
    <div class="vide">Vous n'avez pas encore fait de demande.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Formation demandée</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Réponse de l'admin</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($demandes as $d): ?>
        <tr>
          <td style="font-weight:500;"><?= e($d->nom_formation) ?></td>
          <td><?= e(\Carbon\Carbon::parse($d->date_demande)->format('d/m/Y H:i')) ?></td>
          <td>
            <?php if ($d->statut === 'en_attente'): ?>
              <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:10px;font-size:12px;">En attente</span>
            <?php elseif ($d->statut === 'validee'): ?>
              <span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:10px;font-size:12px;">Validée</span>
            <?php else: ?>
              <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:10px;font-size:12px;">Refusée</span>
            <?php endif; ?>
          </td>
          <td style="font-size:13px;color:var(--gris);">
            <?= e($d->reponse_admin ?? '—') ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
