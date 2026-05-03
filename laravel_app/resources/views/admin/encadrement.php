<?php
$title = 'Encadrement';
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
  <a href="<?= route('admin.encadrement') ?>" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    Encadrement
  </a>
  <a href="<?= route('admin.archivage') ?>" class="nav-item">
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
  <h1>Encadrement</h1>
  <p>Demandes des tuteurs et affectations de jurys.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>
<?php if (session('erreur')): ?>
  <div class="alerte-erreur"><?= e(session('erreur')) ?></div>
<?php endif; ?>

<div class="section-card">
  <div class="section-titre">
    Demandes d'encadrement tuteur
    <?php if ($demandes_en_attente->isNotEmpty()): ?>
      <span style="background:#f59e0b;color:white;font-size:12px;padding:2px 8px;border-radius:10px;margin-left:8px;"><?= e($demandes_en_attente->count()) ?></span>
    <?php endif; ?>
  </div>

  <?php if ($demandes_en_attente->isEmpty()): ?>
    <p style="color:var(--gris);font-size:14px;margin:0;">Aucune demande en attente.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Tuteur</th>
          <th>Étudiant demandé</th>
          <th>N° étudiant</th>
          <th>Date</th>
          <th>Accepter</th>
          <th>Refuser</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($demandes_en_attente as $d): ?>
        <tr>
          <td><?= e($d->utilisateur->prenom ?? '') ?> <?= e($d->utilisateur->nom ?? '') ?></td>
          <td><?= e($d->prenom_etudiant) ?> <?= e($d->nom_etudiant) ?></td>
          <td><?= e($d->numero_etudiant) ?></td>
          <td><?= e(\Carbon\Carbon::parse($d->date_demande)->format('d/m/Y H:i')) ?></td>
          <td>
            <?php
              $correspondantes = $candidatures_validees->filter(function($c) use ($d) {
                return strcasecmp($c->etudiant->utilisateur->nom ?? '',    $d->nom_etudiant) === 0
                    && strcasecmp($c->etudiant->utilisateur->prenom ?? '', $d->prenom_etudiant) === 0
                    && strcasecmp($c->etudiant->numero_etudiant ?? '',     $d->numero_etudiant) === 0;
              });
            ?>
            <?php if ($correspondantes->isEmpty()): ?>
              <span style="color:#dc2626;font-size:12px;">Aucun étudiant ne correspond — refus requis</span>
            <?php else: ?>
              <form method="POST" action="<?= route('admin.accepter-demande', $d->id) ?>" style="display:flex;gap:6px;">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <select name="id_candidature" required style="padding:6px 8px;border:1px solid #ccc;border-radius:6px;font-family:inherit;font-size:13px;min-width:180px;">
                  <?php foreach ($correspondantes as $c): ?>
                    <option value="<?= e($c->id) ?>">
                      <?= e($c->offre->titre ?? 'Offre #'.$c->id_offre) ?>
                      — <?= e($c->offre->entreprise->nom ?? '—') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-valider">Accepter</button>
              </form>
            <?php endif; ?>
          </td>
          <td>
            <form method="POST" action="<?= route('admin.refuser-demande', $d->id) ?>" style="display:flex;gap:6px;">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <input type="text" name="motif_refus" placeholder="Motif (obligatoire)" required maxlength="500"
                     style="padding:6px 8px;border:1px solid #ccc;border-radius:6px;font-family:inherit;font-size:13px;min-width:160px;">
              <button type="submit" class="btn-supprimer">Refuser</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="section-card">
  <div class="section-titre">Affecter un jury à une soutenance</div>
  <?php if ($candidatures_validees->isEmpty() || $jurys->isEmpty()): ?>
    <p style="color:var(--gris);font-size:14px;margin:0;">
      <?php if ($jurys->isEmpty()): ?>
        Aucun jury disponible.
      <?php else: ?>
        Aucune candidature validée pour le moment.
      <?php endif; ?>
    </p>
  <?php else: ?>
    <form method="POST" action="<?= route('admin.affecter-jury') ?>" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
      <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
      <div>
        <label style="display:block;font-size:13px;margin-bottom:4px;">Soutenance (étudiant — stage)</label>
        <select name="id_candidature" required style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;min-width:280px;">
          <?php foreach ($candidatures_validees as $c): ?>
            <option value="<?= e($c->id) ?>">
              <?= e($c->etudiant->utilisateur->prenom ?? '') ?> <?= e($c->etudiant->utilisateur->nom ?? '') ?>
              — <?= e($c->offre->titre ?? 'Offre #'.$c->id_offre) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label style="display:block;font-size:13px;margin-bottom:4px;">Jury</label>
        <select name="id_jury" required style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;min-width:200px;">
          <?php foreach ($jurys as $j): ?>
            <option value="<?= e($j->id) ?>">
              <?= e($j->utilisateur->prenom ?? '') ?> <?= e($j->utilisateur->nom ?? '') ?>
              <?= $j->specialite ? '— '.e($j->specialite) : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn-action">Affecter</button>
    </form>
  <?php endif; ?>
</div>

<div class="section-card">
  <div class="section-titre">Affectations jury actuelles</div>
  <?php if ($affectations_jury->isEmpty()): ?>
    <p style="color:var(--gris);font-size:14px;margin:0;">Aucun jury affecté pour le moment.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Jury</th>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Entreprise</th>
          <th>Depuis</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($affectations_jury as $a): ?>
        <tr>
          <td><?= e($a->jury->utilisateur->prenom ?? '') ?> <?= e($a->jury->utilisateur->nom ?? '') ?></td>
          <td><?= e($a->candidature->etudiant->utilisateur->prenom ?? '') ?> <?= e($a->candidature->etudiant->utilisateur->nom ?? '') ?></td>
          <td><?= e($a->candidature->offre->titre ?? '—') ?></td>
          <td><?= e($a->candidature->offre->entreprise->nom ?? '—') ?></td>
          <td><?= e(\Carbon\Carbon::parse($a->date_affectation)->format('d/m/Y')) ?></td>
          <td>
            <form method="POST" action="<?= route('admin.retirer-jury', $a->id) ?>" style="display:inline;" onsubmit="return confirm('Retirer ce jury de la soutenance ?')">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <button type="submit" class="btn-supprimer">Retirer</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="section-card">
  <div class="section-titre">
    Conventions à valider
    <?php if ($conventions_a_valider->isNotEmpty()): ?>
      <span style="background:#f59e0b;color:white;font-size:12px;padding:2px 8px;border-radius:10px;margin-left:8px;"><?= e($conventions_a_valider->count()) ?></span>
    <?php endif; ?>
  </div>

  <?php if ($conventions_a_valider->isEmpty()): ?>
    <p style="color:var(--gris);font-size:14px;margin:0;">Aucune convention en attente de validation administrative.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Tuteur pédagogique</th>
          <th>Fichier</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($conventions_a_valider as $conv): ?>
        <?php $c = $conv->candidature; $tut = $c->suivi->tuteur ?? null; ?>
        <tr>
          <td><?= e($c->etudiant->utilisateur->prenom ?? '') ?> <?= e($c->etudiant->utilisateur->nom ?? '') ?></td>
          <td><?= e($c->offre->titre ?? '—') ?> — <?= e($c->offre->entreprise->nom ?? '') ?></td>
          <td>
            <?php if ($tut): ?>
              <?= e($tut->utilisateur->prenom ?? '') ?> <?= e($tut->utilisateur->nom ?? '') ?>
            <?php else: ?>
              <span style="color:var(--gris);">—</span>
            <?php endif; ?>
          </td>
          <td><?= e($conv->nom_original ?? basename($conv->chemin_fichier)) ?></td>
          <td style="display:flex;gap:8px;">
            <a href="<?= route('admin.convention.telecharger', $conv->id) ?>" class="btn-action">Télécharger</a>
            <form method="POST" action="<?= route('admin.convention.valider', $conv->id) ?>" style="display:inline;">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <button type="submit" class="btn-valider">Valider</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php if ($demandes_traitees->isNotEmpty()): ?>
<div class="section-card">
  <div class="section-titre">Historique des demandes (20 dernières)</div>
  <table>
    <thead>
      <tr>
        <th>Tuteur</th>
        <th>Étudiant demandé</th>
        <th>Statut</th>
        <th>Détails</th>
        <th>Traitée le</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($demandes_traitees as $d): ?>
      <tr>
        <td><?= e($d->utilisateur->prenom ?? '') ?> <?= e($d->utilisateur->nom ?? '') ?></td>
        <td><?= e($d->prenom_etudiant) ?> <?= e($d->nom_etudiant) ?> (<?= e($d->numero_etudiant) ?>)</td>
        <td>
          <?php if ($d->statut === 'acceptee'): ?>
            <span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:10px;font-size:12px;">Acceptée</span>
          <?php else: ?>
            <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:10px;font-size:12px;">Refusée</span>
          <?php endif; ?>
        </td>
        <td style="font-size:13px;color:var(--gris);">
          <?php if ($d->statut === 'acceptee' && $d->candidature): ?>
            <?= e($d->candidature->offre->titre ?? '—') ?>
          <?php else: ?>
            <?= e($d->motif_refus ?? '—') ?>
          <?php endif; ?>
        </td>
        <td><?= e($d->date_traitement ? \Carbon\Carbon::parse($d->date_traitement)->format('d/m/Y H:i') : '—') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
