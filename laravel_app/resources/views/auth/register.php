<?php
$title = 'Inscription';

ob_start(); ?>
  <div class="auth-titre">Créer un compte</div>
  <div class="auth-sous-titre">Rejoignez la plateforme de stages CY Tech.</div>

  <?php if ($errors->any()): ?>
    <div class="alerte-erreur"><?= e($errors->first()) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= url('/register') ?>" id="form-register">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">

    <div class="row g-2 mb-3">
      <div class="col">
        <label class="form-label">Prénom</label>
        <input type="text" name="prenom" class="form-control" value="<?= e(old('prenom')) ?>" required>
      </div>
      <div class="col">
        <label class="form-label">Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= e(old('nom')) ?>" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= e(old('email')) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Mot de passe <small style="color:var(--gris);font-size:11px">(8 caractères min.)</small></label>
      <input type="password" name="mot_de_passe" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Confirmer le mot de passe</label>
      <input type="password" name="mot_de_passe_confirmation" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Rôle</label>
      <select name="role" class="form-select" id="select-role" required>
        <option value="">Choisir un rôle</option>
        <option value="etudiant"   <?= old('role')=='etudiant'   ? 'selected' : '' ?>>Étudiant</option>
        <option value="tuteur"     <?= old('role')=='tuteur'     ? 'selected' : '' ?>>Tuteur</option>
        <option value="jury"       <?= old('role')=='jury'       ? 'selected' : '' ?>>Jury</option>
        <option value="entreprise" <?= old('role')=='entreprise' ? 'selected' : '' ?>>Entreprise</option>
      </select>
    </div>

    <div id="champs-etudiant" style="display:none">
      <div class="mb-3">
        <label class="form-label">Filière</label>
        <input type="text" name="filiere" class="form-control" value="<?= e(old('filiere', 'Informatique')) ?>">
      </div>
      <div class="row g-2 mb-3">
        <div class="col">
          <label class="form-label">Promotion</label>
          <input type="text" name="promotion" class="form-control" value="<?= e(old('promotion', 'ING1')) ?>">
        </div>
        <div class="col">
          <label class="form-label">N° étudiant</label>
          <input type="text" name="numero_etudiant" class="form-control" value="<?= e(old('numero_etudiant')) ?>">
        </div>
      </div>
    </div>

    <div id="champs-tuteur" style="display:none">
      <div class="mb-3">
        <label class="form-label">Département</label>
        <input type="text" name="departement" class="form-control" value="<?= e(old('departement', 'Informatique')) ?>">
      </div>
    </div>

    <div id="champs-jury" style="display:none">
      <div class="mb-3">
        <label class="form-label">Spécialité</label>
        <input type="text" name="specialite" class="form-control" value="<?= e(old('specialite')) ?>">
      </div>
    </div>

    <div id="champs-entreprise" style="display:none">
      <div class="mb-3">
        <label class="form-label">Nom de l'entreprise</label>
        <input type="text" name="nom_entreprise" class="form-control" value="<?= e(old('nom_entreprise')) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Secteur</label>
        <input type="text" name="secteur" class="form-control" value="<?= e(old('secteur')) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Adresse</label>
        <input type="text" name="adresse" class="form-control" value="<?= e(old('adresse')) ?>">
      </div>
    </div>

    <button type="submit" class="btn-auth">Créer mon compte</button>
  </form>

  <hr class="separateur">

  <div class="auth-link">
    Déjà un compte ? <a href="<?= route('login') ?>">Se connecter</a>
  </div>
<?php $content = ob_get_clean();

ob_start(); ?>
<script>
  const select = document.getElementById('select-role');
  const sections = {
    etudiant:   document.getElementById('champs-etudiant'),
    tuteur:     document.getElementById('champs-tuteur'),
    jury:       document.getElementById('champs-jury'),
    entreprise: document.getElementById('champs-entreprise'),
  };

  function toggleChamps() {
    Object.values(sections).forEach(el => el.style.display = 'none');
    if (sections[select.value]) {
      sections[select.value].style.display = 'block';
    }
  }

  select.addEventListener('change', toggleChamps);
  toggleChamps();
</script>
<?php $scripts = ob_get_clean();

require __DIR__ . '/../layouts/auth.php';
