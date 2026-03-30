@extends('layouts.app')

@section('title', 'Mon tableau de bord')
@section('role-label', 'Étudiant')

@section('nav')
  <a href="{{ route('etudiant.dashboard') }}" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="{{ route('etudiant.dossier') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Mon dossier
  </a>
  <a href="{{ route('etudiant.documents') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
    Documents
  </a>
  <a href="{{ route('etudiant.profil') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    Mon profil
  </a>
@endsection

@section('content')
<div class="page-header">
  <h1>Bonjour, {{ session('prenom') }} 👋</h1>
  <p>Trouvez et postulez à des offres de stage.</p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif
@if(session('erreur'))
  <div class="alerte-erreur">{{ session('erreur') }}</div>
@endif

<div class="kpi-grid-2">
  <div class="kpi-card">
    <div class="kpi-label">Candidatures envoyées</div>
    <div class="kpi-valeur">{{ $nb_candidatures }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Documents déposés</div>
    <div class="kpi-valeur">{{ $nb_documents }}</div>
  </div>
</div>

{{-- Barre de recherche --}}
<div class="section-card">
  <div class="section-titre">Rechercher des offres</div>
  <form method="GET" action="{{ route('etudiant.dashboard') }}" style="display:grid;grid-template-columns:1fr 160px 160px auto;gap:12px;align-items:end;">
    <div>
      <label class="form-label-sm">Mot-clé</label>
      <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Titre, compétence…">
    </div>
    <div>
      <label class="form-label-sm">Durée</label>
      <select name="duree" class="form-select">
        <option value="">Toutes</option>
        @foreach(['1 mois','2 mois','3 mois','4 mois','5 mois','6 mois'] as $d)
          <option value="{{ $d }}" {{ $duree==$d?'selected':'' }}>{{ $d }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="form-label-sm">Lieu</label>
      <input type="text" name="lieu" value="{{ $lieu }}" class="form-control" placeholder="Paris…">
    </div>
    <div>
      <button type="submit" class="btn-valider" style="white-space:nowrap;">Rechercher</button>
    </div>
  </form>
</div>

{{-- Offres --}}
<div class="section-card">
  <div class="section-titre">Offres disponibles</div>
  @if($offres->isEmpty())
    <div class="vide">Aucune offre trouvée.</div>
  @else
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
        @foreach($offres as $offre)
        <tr>
          <td style="font-weight:500;">{{ $offre->titre }}</td>
          <td>{{ $offre->entreprise->nom ?? '—' }}</td>
          <td>{{ $offre->lieu }}</td>
          <td>{{ $offre->duree }}</td>
          <td>{{ \Carbon\Carbon::parse($offre->date_publication)->format('d/m/Y') }}</td>
          <td>
            @if(in_array($offre->id, $offres_postulees))
              <span class="badge-validee">Postulé</span>
            @else
              <form method="POST" action="{{ route('etudiant.postuler') }}" enctype="multipart/form-data" style="display:grid;gap:6px;min-width:220px;">
                @csrf
                <input type="hidden" name="id_offre" value="{{ $offre->id }}">
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
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
