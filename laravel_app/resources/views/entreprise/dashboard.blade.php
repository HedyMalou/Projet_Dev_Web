@extends('layouts.app')

@section('title', 'Tableau de bord entreprise')
@section('role-label', 'Entreprise')

@section('nav')
  <a href="{{ route('entreprise.dashboard') }}" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="{{ route('entreprise.offres') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Mes offres
  </a>
@endsection

@section('content')
<div class="page-header">
  <h1>Bonjour, {{ $entreprise->nom }}</h1>
  <p>Gérez vos offres de stage et les candidatures reçues.</p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif
@if(session('erreur'))
  <div class="alerte-erreur">{{ session('erreur') }}</div>
@endif

<div class="kpi-grid-3">
  <div class="kpi-card">
    <div class="kpi-label">Offres publiées</div>
    <div class="kpi-valeur">{{ $nb_offres }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Candidatures reçues</div>
    <div class="kpi-valeur">{{ $nb_candidatures }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">En attente de réponse</div>
    <div class="kpi-valeur">{{ $nb_en_attente }}</div>
  </div>
</div>

{{-- Publier une offre --}}
<div class="section-card">
  <div class="section-titre">Publier une nouvelle offre</div>
  <form method="POST" action="{{ route('entreprise.publier') }}" style="display:grid;grid-template-columns:1fr 1fr 120px 120px;gap:12px;align-items:end;">
    @csrf
    <div>
      <label class="form-label-sm">Titre *</label>
      <input type="text" name="titre" class="form-control" placeholder="Développeur web…" required>
    </div>
    <div>
      <label class="form-label-sm">Lieu *</label>
      <input type="text" name="lieu" class="form-control" placeholder="Paris" required>
    </div>
    <div>
      <label class="form-label-sm">Durée *</label>
      <select name="duree" class="form-select" required>
        @foreach(['1 mois','2 mois','3 mois','4 mois','5 mois','6 mois'] as $d)
          <option value="{{ $d }}">{{ $d }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <button type="submit" class="btn-valider" style="width:100%;">Publier</button>
    </div>
    <div style="grid-column:1/3;">
      <label class="form-label-sm">Description</label>
      <textarea name="description" class="form-control" rows="2" placeholder="Description du stage…"></textarea>
    </div>
    <div style="grid-column:3/5;">
      <label class="form-label-sm">Compétences requises</label>
      <textarea name="competences" class="form-control" rows="2" placeholder="PHP, Laravel…"></textarea>
    </div>
  </form>
</div>

{{-- Dernières candidatures --}}
<div class="section-card">
  <div class="section-titre">Dernières candidatures</div>
  @if($candidatures->isEmpty())
    <div class="vide">Aucune candidature reçue pour le moment.</div>
  @else
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Offre</th>
          <th>Date</th>
          <th>Statut</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($candidatures->take(10) as $c)
        <tr>
          <td>{{ $c->etudiant->utilisateur->prenom ?? '' }} {{ $c->etudiant->utilisateur->nom ?? '' }}</td>
          <td>{{ $c->offre->titre ?? '—' }}</td>
          <td>{{ \Carbon\Carbon::parse($c->date_candidature)->format('d/m/Y') }}</td>
          <td>
            @if($c->statut === 'en_attente') <span class="badge-attente">En attente</span>
            @elseif($c->statut === 'validee')  <span class="badge-validee">Validée</span>
            @elseif($c->statut === 'refusee')  <span class="badge-refusee">Refusée</span>
            @else                              <span class="badge-archive">Archivée</span>
            @endif
          </td>
          <td>
            <a href="{{ route('entreprise.candidature', $c->id) }}" class="btn-action">Voir</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
