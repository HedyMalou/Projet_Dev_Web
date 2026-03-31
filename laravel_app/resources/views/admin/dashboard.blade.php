@extends('layouts.app')

@section('title', 'Administration')
@section('role-label', 'Administrateur')

@section('nav')
  <a href="{{ route('admin.dashboard') }}" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="{{ route('admin.offres') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Offres
  </a>
  <a href="{{ route('admin.archivage') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
    Archivage
  </a>
@endsection

@section('content')
<div class="page-header">
  <h1>Administration</h1>
  <p>Vue d'ensemble de la plateforme.</p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif
@if(session('erreur'))
  <div class="alerte-erreur">{{ session('erreur') }}</div>
@endif

<div class="kpi-grid-4">
  <div class="kpi-card">
    <div class="kpi-label">Étudiants inscrits</div>
    <div class="kpi-valeur">{{ $nb_etudiants }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Offres publiées</div>
    <div class="kpi-valeur">{{ $nb_offres }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Stages validés</div>
    <div class="kpi-valeur">{{ $nb_stages }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Utilisateurs</div>
    <div class="kpi-valeur">{{ $nb_users }}</div>
  </div>
</div>

{{-- Comptes en attente de validation --}}
<div class="section-card">
  <div class="section-titre">
    Comptes en attente de validation
    @if($comptes_en_attente->isNotEmpty())
      <span style="background:#f59e0b;color:white;font-size:12px;padding:2px 8px;border-radius:10px;margin-left:8px;">{{ $comptes_en_attente->count() }}</span>
    @endif
  </div>
  @if($comptes_en_attente->isEmpty())
    <p style="color:var(--gris);font-size:14px;margin:0;">Aucun compte en attente de validation.</p>
  @else
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
      @foreach($comptes_en_attente as $u)
      <tr>
        <td>{{ $u->prenom }} {{ $u->nom }}</td>
        <td>{{ $u->email }}</td>
        <td><span class="badge-role">{{ ucfirst($u->role) }}</span></td>
        <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') }}</td>
        <td style="display:flex;gap:8px;">
          <form method="POST" action="{{ route('admin.valider-compte', $u->id) }}">
            @csrf
            <button type="submit" class="btn-valider">Valider</button>
          </form>
          <form method="POST" action="{{ route('admin.refuser-compte', $u->id) }}" onsubmit="return confirm('Refuser et supprimer ce compte ?')">
            @csrf
            <button type="submit" class="btn-supprimer">Refuser</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
</div>

{{-- Affecter un tuteur à un étudiant --}}
<div class="section-card">
  <div class="section-titre">Affecter un tuteur à un étudiant</div>
  @if($candidatures_validees->isEmpty() || $tuteurs->isEmpty())
    <p style="color:var(--gris);font-size:14px;margin:0;">
      @if($tuteurs->isEmpty())
        Aucun tuteur disponible.
      @else
        Aucune candidature validée pour le moment.
      @endif
    </p>
  @else
  <form method="POST" action="{{ route('admin.affecter-tuteur') }}" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
    @csrf
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Étudiant — Offre</label>
      <select name="id_candidature" required style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;min-width:260px;">
        @foreach($candidatures_validees as $c)
          <option value="{{ $c->id }}">
            {{ $c->etudiant->utilisateur->prenom ?? '' }} {{ $c->etudiant->utilisateur->nom ?? '' }}
            — {{ $c->offre->titre ?? 'Offre #'.$c->id_offre }}
          </option>
        @endforeach
      </select>
    </div>
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Tuteur</label>
      <select name="id_tuteur" required style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;min-width:200px;">
        @foreach($tuteurs as $t)
          <option value="{{ $t->id }}">
            {{ $t->utilisateur->prenom ?? '' }} {{ $t->utilisateur->nom ?? '' }}
          </option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn-action">Affecter</button>
  </form>
  @endif
</div>

{{-- Gestion utilisateurs --}}
<div class="section-card">
  <div class="section-titre">Gestion des utilisateurs</div>

  {{-- Barre recherche + filtre rôle --}}
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
        <th>Inscrit le</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $u)
      <tr data-nom="{{ strtolower($u->prenom.' '.$u->nom) }}" data-email="{{ strtolower($u->email) }}" data-role="{{ $u->role }}">
        <td>{{ $u->prenom }} {{ $u->nom }}</td>
        <td>{{ $u->email }}</td>
        <td>
          <span class="badge-role {{ $u->role === 'admin' ? 'badge-admin' : '' }}">{{ ucfirst($u->role) }}</span>
        </td>
        <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') }}</td>
        <td>
          @if($u->id !== session('user_id'))
          <form method="POST" action="{{ route('admin.supprimer-user') }}" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?')">
            @csrf
            <input type="hidden" name="supprimer_id" value="{{ $u->id }}">
            <button type="submit" class="btn-supprimer">Supprimer</button>
          </form>
          @else
            <span style="font-size:12px;color:var(--gris);">Vous</span>
          @endif
        </td>
      </tr>
      @endforeach
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
@endsection
