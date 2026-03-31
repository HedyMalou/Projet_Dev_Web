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
@if($comptes_en_attente->isNotEmpty())
<div class="section-card">
  <div class="section-titre">Comptes en attente de validation ({{ $comptes_en_attente->count() }})</div>
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
          <form method="POST" action="{{ route('admin.valider-compte', $u->id) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn-valider">Valider</button>
          </form>
          <form method="POST" action="{{ route('admin.refuser-compte', $u->id) }}" style="display:inline;" onsubmit="return confirm('Refuser et supprimer ce compte ?')">
            @csrf
            <button type="submit" class="btn-supprimer">Refuser</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

{{-- Affecter un tuteur à un étudiant --}}
@if($candidatures_validees->isNotEmpty() && $tuteurs->isNotEmpty())
<div class="section-card">
  <div class="section-titre">Affecter un tuteur à un étudiant</div>
  <form method="POST" action="{{ route('admin.affecter-tuteur') }}" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
    @csrf
    <div>
      <label style="display:block;font-size:13px;margin-bottom:4px;">Candidature validée</label>
      <select name="id_candidature" required style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;">
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
      <select name="id_tuteur" required style="padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-family:inherit;">
        @foreach($tuteurs as $t)
          <option value="{{ $t->id }}">
            {{ $t->utilisateur->prenom ?? '' }} {{ $t->utilisateur->nom ?? '' }}
          </option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn-action">Affecter</button>
  </form>
</div>
@endif

{{-- Gestion utilisateurs --}}
<div class="section-card">
  <div class="section-titre">Gestion des utilisateurs</div>
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
      @foreach($users as $u)
      <tr>
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
</div>
@endsection
