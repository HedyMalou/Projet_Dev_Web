<?php
$title = 'Mon dossier';
$role_label = 'Étudiant';

ob_start(); ?>
  <a href="<?= route('etudiant.dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="<?= route('etudiant.dossier') ?>" class="nav-item active">
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
  <a href="<?= route('etudiant.profil') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    Mon profil
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Mon dossier</h1>
  <p>Suivez vos candidatures et conventions.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>
<?php if (session('erreur')): ?>
  <div class="alerte-erreur"><?= e(session('erreur')) ?></div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Mes candidatures</div>
  <?php if ($candidatures->isEmpty()): ?>
    <div class="vide">Vous n'avez encore postulé à aucune offre.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Offre</th>
          <th>Entreprise</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Convention</th>
          <th>Documents</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($candidatures as $c): ?>
        <tr>
          <td style="font-weight:500;"><?= e($c->offre->titre ?? '—') ?></td>
          <td><?= e($c->offre->entreprise->nom ?? '—') ?></td>
          <td><?= e(\Carbon\Carbon::parse($c->date_candidature)->format('d/m/Y')) ?></td>
          <td>
            <?php if ($c->statut === 'en_attente'): ?> <span class="badge-attente">En attente</span>
            <?php elseif ($c->statut === 'validee'): ?>  <span class="badge-validee">Validée</span>
            <?php elseif ($c->statut === 'refusee'): ?>  <span class="badge-refusee">Refusée</span>
            <?php else: ?>                                 <span class="badge-archive">Archivée</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($c->convention): ?>
              <span class="badge-validee">Signée</span>
            <?php else: ?>
              <span style="color:var(--gris);font-size:12px;">—</span>
            <?php endif; ?>
          </td>
          <td>
            <?= e(count($documents_par_cand[$c->id] ?? [])) ?> fichier(s)
          </td>
          <td>
            <?php if ($c->statut === 'en_attente'): ?>
              <form method="POST" action="<?= route('etudiant.annuler-candidature', $c->id) ?>" style="display:inline;"
                    onsubmit="return confirm('Annuler cette candidature ? Les documents seront supprimés définitivement.')">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <button type="submit" class="btn-supprimer">Annuler</button>
              </form>
            <?php else: ?>
              <span style="color:var(--gris);font-size:12px;">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php $cand_avec_missions = $candidatures->filter(fn($c) => $c->missions->isNotEmpty()); ?>
<?php if ($cand_avec_missions->isNotEmpty()): ?>
  <?php foreach ($cand_avec_missions as $c): ?>
  <div class="section-card">
    <div class="section-titre">Missions — <?= e($c->offre->titre ?? '—') ?></div>
    <table>
      <thead><tr><th>Titre</th><th>Description</th><th>Statut</th></tr></thead>
      <tbody>
        <?php foreach ($c->missions as $m): ?>
        <tr>
          <td><strong><?= e($m->titre) ?></strong></td>
          <td style="color:var(--gris);font-size:13px;"><?= e($m->description ?? '—') ?></td>
          <td>
            <?php if ($m->statut === 'terminee'): ?>
              <span class="badge-validee">Terminée</span>
            <?php else: ?>
              <span class="badge-attente">En cours</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php $cand_validees = $candidatures->whereIn('statut', ['validee','archivee']); ?>
<?php if ($cand_validees->isNotEmpty()): ?>
  <?php foreach ($cand_validees as $c): ?>
  <div class="section-card">
    <div class="section-titre" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
      <span>
        Convention — <?= e($c->offre->titre ?? '—') ?>
        <span style="color:var(--gris);font-weight:400;"> — <?= e($c->offre->entreprise->nom ?? '—') ?></span>
      </span>
    </div>

    <?php $conv = $c->convention; ?>
    <?php if (!$conv || !$conv->chemin_fichier): ?>
      <p style="color:var(--gris);font-size:14px;margin:0 0 12px;">Déposez votre convention de stage signée (PDF, max 5 Mo).</p>
      <form method="POST" action="<?= route('etudiant.convention.upload') ?>" enctype="multipart/form-data" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap;">
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id_candidature" value="<?= e($c->id) ?>">
        <div>
          <label class="form-label-sm">Fichier convention</label>
          <input type="file" name="fichier" class="form-control" accept=".pdf,.doc,.docx" required>
        </div>
        <button type="submit" class="btn-valider">Déposer</button>
      </form>
    <?php else: ?>
      <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
        <div style="font-size:13px;">
          <strong><?= e($conv->nom_original ?? basename($conv->chemin_fichier)) ?></strong>
        </div>
        <a href="<?= route('etudiant.convention.telecharger', $conv->id) ?>" class="btn-action">Télécharger</a>
      </div>
      <table>
        <thead>
          <tr>
            <th>Étape</th>
            <th>Statut</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Dépôt étudiant</td>
            <td>
              <?php if ($conv->statut_etudiant === 'signe'): ?>
                <span class="badge-validee">Déposée</span>
              <?php else: ?>
                <span class="badge-attente">En attente</span>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td>Validation tuteur pédagogique</td>
            <td>
              <?php if ($conv->statut_tuteur === 'signe'): ?>
                <span class="badge-validee">Validée</span>
              <?php else: ?>
                <span class="badge-attente">En attente</span>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td>Validation entreprise</td>
            <td>
              <?php if ($conv->statut_entreprise === 'signe'): ?>
                <span class="badge-validee">Validée</span>
              <?php else: ?>
                <span class="badge-attente">En attente</span>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td>Validation administration</td>
            <td>
              <?php if ($conv->statut_admin === 'signe'): ?>
                <span class="badge-validee">Validée — convention finalisée</span>
              <?php else: ?>
                <span class="badge-attente">En attente</span>
              <?php endif; ?>
            </td>
          </tr>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php $cand_actives = $candidatures->whereIn('statut', ['validee','archivee']); ?>
<?php if ($cand_actives->isNotEmpty()): ?>
<div class="section-card">
  <div class="section-titre">Messages avec votre tuteur</div>

  <?php

    $commentaires_par_cand = $commentaires->groupBy('id_candidature');
  ?>

  <?php foreach ($cand_actives as $c): ?>
    <div style="margin-bottom:18px;padding-bottom:18px;border-bottom:0.5px solid var(--bordure);">
      <div style="font-size:13px;font-weight:600;margin-bottom:10px;">
        <?= e($c->offre->titre ?? '—') ?>
        <span style="color:var(--gris);font-weight:400;"> — <?= e($c->offre->entreprise->nom ?? '') ?></span>
      </div>

      <?php $msgs = $commentaires_par_cand[$c->id] ?? collect(); ?>
      <?php if ($msgs->isEmpty()): ?>
        <p style="color:var(--gris);font-size:13px;margin:0 0 10px;">Aucun échange pour le moment.</p>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
          <?php foreach ($msgs->sortBy('date') as $com): ?>
            <?php $estMoi = ((int)$com->id_utilisateur === (int)session('user_id')); ?>
            <div style="background:<?= $estMoi ? '#eaf1fa' : '#f3f6fb' ?>;padding:10px 12px;border-radius:8px;align-self:<?= $estMoi ? 'flex-end' : 'flex-start' ?>;max-width:80%;">
              <div style="font-size:11px;color:var(--gris);margin-bottom:3px;">
                <?= e($com->utilisateur->prenom ?? '') ?> <?= e($com->utilisateur->nom ?? '') ?>
                — <?= e(\Carbon\Carbon::parse($com->date)->format('d/m/Y H:i')) ?>
              </div>
              <div style="font-size:13px;"><?= e($com->contenu) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?= route('etudiant.commenter') ?>" style="display:flex;gap:6px;">
        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id_candidature" value="<?= e($c->id) ?>">
        <input type="text" name="contenu" class="form-control" placeholder="Écrire un message à votre tuteur…" required maxlength="2000" style="flex:1;">
        <button type="submit" class="btn-action">Envoyer</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
