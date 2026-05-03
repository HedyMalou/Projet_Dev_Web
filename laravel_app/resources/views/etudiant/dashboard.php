<?php
$title = 'Mon tableau de bord';
$role_label = 'Étudiant';

ob_start(); ?>
  <a href="<?= route('etudiant.dashboard') ?>" class="nav-item active">
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
  <a href="<?= route('etudiant.profil') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    Mon profil
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Bonjour, <?= e(session('prenom')) ?> 👋</h1>
  <p>Trouvez et postulez à des offres de stage.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>
<?php if (session('erreur')): ?>
  <div class="alerte-erreur"><?= e(session('erreur')) ?></div>
<?php endif; ?>
<?php if ($errors->any()): ?>
  <div class="alerte-erreur">
    <?php foreach ($errors->all() as $erreur): ?> <?= e($erreur) ?><br> <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="kpi-grid-2">
  <div class="kpi-card">
    <div class="kpi-label">Candidatures envoyées</div>
    <div class="kpi-valeur"><?= e($nb_candidatures) ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Documents déposés</div>
    <div class="kpi-valeur"><?= e($nb_documents) ?></div>
  </div>
</div>

<div class="section-card">
  <div class="section-titre">Rechercher des offres</div>
  <form method="GET" action="<?= route('etudiant.dashboard') ?>" style="display:grid;grid-template-columns:1fr 150px 140px 140px auto;gap:12px;align-items:end;">
    <div>
      <label class="form-label-sm">Mot-clé</label>
      <input type="text" name="q" value="<?= e($q) ?>" class="form-control" placeholder="Titre, compétence…">
    </div>
    <div>
      <label class="form-label-sm">Filière</label>
      <select name="filiere" class="form-select">
        <option value="">Toutes</option>
        <?php foreach (['Informatique','Mathématiques','Génie civil','Mécanique','Biotechnologies','Électronique'] as $f): ?>
          <option value="<?= e($f) ?>" <?= $filiere==$f?'selected':'' ?>><?= e($f) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="form-label-sm">Durée</label>
      <select name="duree" class="form-select">
        <option value="">Toutes</option>
        <?php foreach (['1 mois','2 mois','3 mois','4 mois','5 mois','6 mois'] as $d): ?>
          <option value="<?= e($d) ?>" <?= $duree==$d?'selected':'' ?>><?= e($d) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="form-label-sm">Lieu</label>
      <input type="text" name="lieu" value="<?= e($lieu) ?>" class="form-control" placeholder="Paris…">
    </div>
    <div>
      <button type="submit" class="btn-valider" style="white-space:nowrap;">Rechercher</button>
    </div>
  </form>
</div>

<div class="section-card">
  <div class="section-titre">Offres disponibles</div>
  <?php if ($offres->isEmpty()): ?>
    <div class="vide">Aucune offre trouvée.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Entreprise</th>
          <th>Lieu</th>
          <th>Durée</th>
          <th>Publié le</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($offres as $offre): ?>
        <tr>
          <td style="font-weight:500;"><?= e($offre->titre) ?></td>
          <td><?= e($offre->entreprise->nom ?? '—') ?></td>
          <td><?= e($offre->lieu) ?></td>
          <td><?= e($offre->duree) ?></td>
          <td><?= e(\Carbon\Carbon::parse($offre->date_publication)->format('d/m/Y')) ?></td>
          <td>
            <?php $cand = $candidatures_par_offre[$offre->id] ?? null; ?>
            <?php if ($cand): ?>
              <?php if ($cand->statut === 'en_attente'): ?>
                <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start;">
                  <span class="badge-attente">En attente</span>
                  <form method="POST" action="<?= route('etudiant.annuler-candidature', $cand->id) ?>" style="display:inline;"
                        onsubmit="return confirm('Annuler cette candidature ? Les documents seront supprimés définitivement.')">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="btn-supprimer">Annuler</button>
                  </form>
                </div>
              <?php elseif ($cand->statut === 'validee'): ?>
                <span class="badge-validee">Validée</span>
              <?php elseif ($cand->statut === 'refusee'): ?>
                <span class="badge-refusee">Refusée</span>
              <?php else: ?>
                <span class="badge-archive">Archivée</span>
              <?php endif; ?>
            <?php else: ?>
              <form method="POST" action="<?= route('etudiant.postuler') ?>" enctype="multipart/form-data" style="display:grid;gap:6px;min-width:220px;">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id_offre" value="<?= e($offre->id) ?>">
                <div>
                  <label class="form-label-sm">CV (PDF/DOC)</label>
                  <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx" required>
                </div>
                <div>
                  <label class="form-label-sm">Lettre de motivation (PDF/DOC)</label>
                  <input type="file" name="lettre_motivation" class="form-control" accept=".pdf,.doc,.docx" required>
                </div>
                <button type="submit" class="btn-action">Postuler</button>
              </form>
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
