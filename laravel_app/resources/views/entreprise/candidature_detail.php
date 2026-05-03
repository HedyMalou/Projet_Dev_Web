<?php
$title = 'Détail candidature';
$role_label = 'Entreprise';

ob_start(); ?>
  <a href="<?= route('entreprise.dashboard') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="<?= route('entreprise.offres') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Mes offres
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Candidature — <?= e($candidature->offre->titre ?? '—') ?></h1>
  <p><a href="<?= route('entreprise.dashboard') ?>" style="color:var(--bleu-clair);">← Retour</a></p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Informations étudiant</div>
  <table>
    <tbody>
      <tr><td style="color:var(--gris);width:180px;">Nom</td><td><?= e($candidature->etudiant->utilisateur->nom ?? '—') ?></td></tr>
      <tr><td style="color:var(--gris);">Prénom</td><td><?= e($candidature->etudiant->utilisateur->prenom ?? '—') ?></td></tr>
      <tr><td style="color:var(--gris);">Email</td><td><?= e($candidature->etudiant->utilisateur->email ?? '—') ?></td></tr>
      <tr>
        <td style="color:var(--gris);">Statut</td>
        <td>
          <?php if ($candidature->statut === 'en_attente'): ?> <span class="badge-attente">En attente</span>
          <?php elseif ($candidature->statut === 'validee'): ?>  <span class="badge-validee">Validée</span>
          <?php elseif ($candidature->statut === 'refusee'): ?>  <span class="badge-refusee">Refusée</span>
          <?php else: ?>                                           <span class="badge-archive">Archivée</span>
          <?php endif; ?>
        </td>
      </tr>
      <tr><td style="color:var(--gris);">Date candidature</td><td><?= e(\Carbon\Carbon::parse($candidature->date_candidature)->format('d/m/Y')) ?></td></tr>
    </tbody>
  </table>
</div>

<?php if ($candidature->documents->isNotEmpty()): ?>
<div class="section-card">
  <div class="section-titre">Documents fournis</div>
  <table>
    <thead>
      <tr>
        <th>Type</th>
        <th>Nom du fichier</th>
        <th>Déposé le</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php
        $labels = ['cv' => 'CV', 'lettre_motivation' => 'Lettre de motivation', 'rapport' => 'Rapport', 'resume' => 'Résumé', 'fiche_evaluation' => 'Fiche d\'évaluation', 'convention' => 'Convention', 'autre' => 'Autre'];
      ?>
      <?php foreach ($candidature->documents as $doc): ?>
      <tr>
        <td><?= e($labels[$doc->type] ?? $doc->type) ?></td>
        <td><?= e($doc->nom_original ?? basename($doc->chemin_fichier)) ?></td>
        <td><?= e(\Carbon\Carbon::parse($doc->date_depot)->format('d/m/Y')) ?></td>
        <td>
          <a href="<?= route('entreprise.documents.telecharger', $doc->id) ?>" class="btn-action">Télécharger</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php if (in_array($candidature->statut, ['validee','archivee']) && $candidature->convention): ?>
<?php $conv = $candidature->convention; ?>
<div class="section-card">
  <div class="section-titre">Convention de stage</div>
  <?php if (!$conv->chemin_fichier): ?>
    <p style="color:var(--gris);font-size:14px;margin:0;">L'étudiant n'a pas encore déposé la convention.</p>
  <?php else: ?>
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
      <div style="font-size:13px;"><strong><?= e($conv->nom_original ?? basename($conv->chemin_fichier)) ?></strong></div>
      <a href="<?= route('entreprise.convention.telecharger', $conv->id) ?>" class="btn-action">Télécharger</a>
    </div>
    <table>
      <thead><tr><th>Étape</th><th>Statut</th></tr></thead>
      <tbody>
        <tr>
          <td>Dépôt étudiant</td>
          <td><?= $conv->statut_etudiant === 'signe' ? '<span class="badge-validee">Déposée</span>' : '<span class="badge-attente">En attente</span>' ?></td>
        </tr>
        <tr>
          <td>Validation entreprise (vous)</td>
          <td>
            <?php if ($conv->statut_entreprise === 'signe'): ?>
              <span class="badge-validee">Validée</span>
            <?php elseif ($conv->statut_etudiant === 'signe'): ?>
              <form method="POST" action="<?= route('entreprise.convention.valider', $conv->id) ?>" style="display:inline;">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <button type="submit" class="btn-valider">Valider</button>
              </form>
            <?php else: ?>
              <span style="color:var(--gris);font-size:12px;">En attente du dépôt étudiant</span>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td>Validation tuteur pédagogique</td>
          <td><?= $conv->statut_tuteur === 'signe' ? '<span class="badge-validee">Validée</span>' : '<span class="badge-attente">En attente</span>' ?></td>
        </tr>
        <tr>
          <td>Validation administration</td>
          <td><?= $conv->statut_admin === 'signe' ? '<span class="badge-validee">Convention finalisée</span>' : '<span class="badge-attente">En attente</span>' ?></td>
        </tr>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php if (in_array($candidature->statut, ['validee','archivee'])): ?>
<div class="section-card">
  <div class="section-titre">Missions attribuées à l'étudiant</div>

  <?php if ($candidature->missions->isEmpty()): ?>
    <p style="color:var(--gris);font-size:13px;margin:0 0 12px;">Aucune mission attribuée pour le moment.</p>
  <?php else: ?>
    <table style="margin-bottom:14px;">
      <thead>
        <tr><th>Titre</th><th>Description</th><th>Statut</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($candidature->missions as $m): ?>
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
          <td>
            <?php if ($m->statut === 'en_cours'): ?>
              <form method="POST" action="<?= route('entreprise.mission.terminer', $m->id) ?>" style="display:inline;">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <button type="submit" class="btn-action">Marquer terminée</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <form method="POST" action="<?= route('entreprise.mission.creer') ?>" style="display:grid;grid-template-columns:1fr 2fr auto;gap:8px;align-items:end;">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id_candidature" value="<?= e($candidature->id) ?>">
    <div>
      <label class="form-label-sm">Titre</label>
      <input type="text" name="titre" class="form-control" placeholder="Ex: Refonte page d'accueil" required maxlength="200">
    </div>
    <div>
      <label class="form-label-sm">Description</label>
      <input type="text" name="description" class="form-control" placeholder="Détails…" maxlength="2000">
    </div>
    <button type="submit" class="btn-valider">Attribuer</button>
  </form>
</div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">Remarques sur le stage</div>
  <?php if ($candidature->commentaires->isEmpty()): ?>
    <p style="color:var(--gris);font-size:13px;margin:0 0 12px;">Aucune remarque pour le moment.</p>
  <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px;">
      <?php foreach ($candidature->commentaires->sortBy('date') as $com): ?>
        <div style="background:#f3f6fb;padding:10px 12px;border-radius:8px;">
          <div style="font-size:11px;color:var(--gris);margin-bottom:3px;">
            <?= e($com->utilisateur->prenom ?? '') ?> <?= e($com->utilisateur->nom ?? '') ?>
            — <?= e(\Carbon\Carbon::parse($com->date)->format('d/m/Y H:i')) ?>
          </div>
          <div style="font-size:13px;"><?= e($com->contenu) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <form method="POST" action="<?= route('entreprise.commenter') ?>" style="display:flex;gap:6px;">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id_candidature" value="<?= e($candidature->id) ?>">
    <input type="text" name="contenu" class="form-control" placeholder="Ajouter une remarque…" required maxlength="2000" style="flex:1;">
    <button type="submit" class="btn-action">Envoyer</button>
  </form>
</div>

<?php if ($candidature->statut === 'en_attente'): ?>
<div class="section-card">
  <div class="section-titre">Décision</div>
  <form method="POST" action="<?= route('entreprise.valider') ?>" style="display:flex;gap:12px;">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id_candidature" value="<?= e($candidature->id) ?>">
    <button type="submit" name="statut" value="validee" class="btn-valider">Valider la candidature</button>
    <button type="submit" name="statut" value="refusee" class="btn-supprimer" style="padding:9px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Refuser</button>
  </form>
</div>
<?php endif; ?>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
