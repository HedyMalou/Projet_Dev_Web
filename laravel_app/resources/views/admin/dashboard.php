<?php
$title = 'Administration';
$role_label = 'Administrateur';

ob_start(); ?>
  <a href="<?= route('admin.dashboard') ?>" class="nav-item active">
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
  <a href="<?= route('admin.demandes-formation') ?>" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Demandes formation
  </a>
<?php $nav = ob_get_clean();

ob_start(); ?>
<div class="page-header">
  <h1>Administration</h1>
  <p>Vue d'ensemble de la plateforme.</p>
</div>

<?php if (session('succes')): ?>
  <div class="alerte-succes"><?= e(session('succes')) ?></div>
<?php endif; ?>
<?php if (session('erreur')): ?>
  <div class="alerte-erreur"><?= e(session('erreur')) ?></div>
<?php endif; ?>

<div class="kpi-grid-4">
  <div class="kpi-card">
    <div class="kpi-label">Étudiants inscrits</div>
    <div class="kpi-valeur"><?= e($nb_etudiants) ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Offres publiées</div>
    <div class="kpi-valeur"><?= e($nb_offres) ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Stages validés</div>
    <div class="kpi-valeur"><?= e($nb_stages) ?></div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Utilisateurs</div>
    <div class="kpi-valeur"><?= e($nb_users) ?></div>
  </div>
</div>

<div class="section-card">
  <div class="section-titre">
    Comptes en attente de validation
    <?php if ($comptes_en_attente->isNotEmpty()): ?>
      <span style="background:#f59e0b;color:white;font-size:12px;padding:2px 8px;border-radius:10px;margin-left:8px;"><?= e($comptes_en_attente->count()) ?></span>
    <?php endif; ?>
  </div>
  <?php if ($comptes_en_attente->isEmpty()): ?>
    <p style="color:var(--gris);font-size:14px;margin:0;">Aucun compte en attente de validation.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Inscrit le</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($comptes_en_attente as $u): ?>
      <tr>
        <td><?= e($u->prenom) ?> <?= e($u->nom) ?></td>
        <td><?= e($u->email) ?></td>
        <td><span class="badge-role"><?= e(ucfirst($u->role)) ?></span></td>
        <td><?= e(\Carbon\Carbon::parse($u->created_at)->format('d/m/Y')) ?></td>
        <td style="display:flex;gap:8px;">
          <form method="POST" action="<?= route('admin.valider-compte', $u->id) ?>">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
            <button type="submit" class="btn-valider">Valider</button>
          </form>
          <form method="POST" action="<?= route('admin.refuser-compte', $u->id) ?>" onsubmit="return confirm('Refuser et supprimer ce compte ?')">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
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
  <div class="section-titre">Affecter un tuteur à un étudiant</div>
  <?php if ($candidatures_validees->isEmpty() || $tuteurs->isEmpty()): ?>
    <p style="color:var(--gris);font-size:14px;margin:0;">
      <?php if ($tuteurs->isEmpty()): ?>
        Aucun tuteur disponible.
      <?php else: ?>
        Aucune candidature validée pour le moment.
      <?php endif; ?>
    </p>
  <?php else: ?>
  <form method="POST" action="<?= route('admin.affecter-tuteur') ?>" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Étudiant — Offre</label>
      <select name="id_candidature" required style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;min-width:260px;">
        <?php foreach ($candidatures_validees as $c): ?>
          <option value="<?= e($c->id) ?>">
            <?= e($c->etudiant->utilisateur->prenom ?? '') ?> <?= e($c->etudiant->utilisateur->nom ?? '') ?>
            — <?= e($c->offre->titre ?? 'Offre #'.$c->id_offre) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Tuteur</label>
      <select name="id_tuteur" required style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;min-width:200px;">
        <?php foreach ($tuteurs as $t): ?>
          <option value="<?= e($t->id) ?>">
            <?= e($t->utilisateur->prenom ?? '') ?> <?= e($t->utilisateur->nom ?? '') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn-action">Affecter</button>
  </form>
  <?php endif; ?>
</div>

<div class="section-card">
  <div class="section-titre">Gestion des utilisateurs</div>

  <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
    <input
      type="text"
      id="recherche-users"
      placeholder="Rechercher par nom ou email…"
      style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;font-size:13px;flex:1;min-width:200px;"
      oninput="filtrerUsers()"
    >
    <select
      id="filtre-role"
      style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;font-size:13px;"
      onchange="filtrerUsers()"
    >
      <option value="">Tous les rôles</option>
      <option value="etudiant">Étudiant</option>
      <option value="tuteur">Tuteur</option>
      <option value="jury">Jury</option>
      <option value="entreprise">Entreprise</option>
      <option value="admin">Admin</option>
    </select>
  </div>

  <table id="table-users">
    <thead>
      <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Accès</th>
        <th>Actions</th>
        <th>Dernière activité</th>
        <th>Inscrit le</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <?php
        $stats = $activites[$u->id] ?? collect();
        $nbAcces  = optional($stats->firstWhere('type','acces'))->nb ?? 0;
        $nbAction = optional($stats->firstWhere('type','action'))->nb ?? 0;
        $dernier  = $stats->max('dernier');
      ?>
      <tr data-nom="<?= e(strtolower($u->prenom.' '.$u->nom)) ?>" data-email="<?= e(strtolower($u->email)) ?>" data-role="<?= e($u->role) ?>">
        <td><a href="<?= route('utilisateur.profil', $u->id) ?>" style="color:var(--bleu-clair);text-decoration:none;"><?= e($u->prenom) ?> <?= e($u->nom) ?></a></td>
        <td><?= e($u->email) ?></td>
        <td>
          <?php if ($u->id !== session('user_id')): ?>
            <form method="POST" action="<?= route('admin.modifier-role', $u->id) ?>" style="display:inline-flex;gap:4px;align-items:center;"
                  onsubmit="return confirm('Changer le rôle de cet utilisateur ? Son ancien profil sera supprimé.')">
              <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
              <select name="role" class="form-select" style="padding:4px 8px;font-size:12px;width:auto;">
                <?php foreach (['etudiant','tuteur','jury','entreprise','admin'] as $r): ?>
                  <option value="<?= e($r) ?>" <?= $u->role===$r?'selected':'' ?>><?= e(ucfirst($r)) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn-action" style="padding:3px 8px;font-size:11px;">Changer</button>
            </form>
          <?php else: ?>
            <span class="badge-role badge-admin"><?= e(ucfirst($u->role)) ?></span>
          <?php endif; ?>
        </td>
        <td><?= e($nbAcces) ?></td>
        <td><?= e($nbAction) ?></td>
        <td style="font-size:12px;color:var(--gris);"><?= $dernier ? e(\Carbon\Carbon::parse($dernier)->format('d/m/Y H:i')) : '—' ?></td>
        <td><?= e(\Carbon\Carbon::parse($u->created_at)->format('d/m/Y')) ?></td>
        <td>
          <?php if ($u->id !== session('user_id')): ?>
          <form method="POST" action="<?= route('admin.supprimer-user') ?>" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?')">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="supprimer_id" value="<?= e($u->id) ?>">
            <button type="submit" class="btn-supprimer">Supprimer</button>
          </form>
          <?php else: ?>
            <span style="font-size:12px;color:var(--gris);">Vous</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p id="aucun-resultat" style="display:none;color:var(--gris);font-size:14px;margin:8px 0 0;">Aucun résultat.</p>
</div>

<script>
function filtrerUsers() {
  const recherche = document.getElementById('recherche-users').value.toLowerCase();
  const role = document.getElementById('filtre-role').value;
  const lignes = document.querySelectorAll('#table-users tbody tr');
  let visible = 0;

  lignes.forEach(function(ligne) {
    const nom   = ligne.dataset.nom   || '';
    const email = ligne.dataset.email || '';
    const r     = ligne.dataset.role  || '';

    const matchRecherche = nom.includes(recherche) || email.includes(recherche);
    const matchRole = role === '' || r === role;

    if (matchRecherche && matchRole) {
      ligne.style.display = '';
      visible++;
    } else {
      ligne.style.display = 'none';
    }
  });

  document.getElementById('aucun-resultat').style.display = visible === 0 ? '' : 'none';
}
</script>
<?php $content = ob_get_clean();

require __DIR__ . '/../layouts/app.php';
