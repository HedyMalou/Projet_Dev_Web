@extends('layouts.app')

@section('title', 'Mes documents')
@section('role-label', 'Étudiant')

@section('nav')
  <a href="{{ route('etudiant.dashboard') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="{{ route('etudiant.dossier') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Mon dossier
  </a>
  <a href="{{ route('etudiant.documents') }}" class="nav-item active">
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
  <h1>Mes documents</h1>
  <p>Déposez et consultez vos documents de stage.</p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif
@if(session('erreur'))
  <div class="alerte-erreur">{{ session('erreur') }}</div>
@endif

{{-- Formulaire de dépôt --}}
@if($candidatures_valides->isNotEmpty())
<div class="section-card">
  <div class="section-titre">Déposer un document</div>
  <form method="POST" action="{{ route('etudiant.documents.upload') }}" enctype="multipart/form-data"
        style="display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end;">
    @csrf
    <div>
      <label class="form-label-sm">Candidature</label>
      <select name="id_candidature" class="form-select" required>
        @foreach($candidatures_valides as $c)
          <option value="{{ $c->id }}">{{ $c->offre->titre ?? '—' }} — {{ $c->offre->entreprise->nom ?? '' }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="form-label-sm">Fichier (PDF, max 5 Mo)</label>
      <input type="file" name="fichier" class="form-control" accept=".pdf,.doc,.docx" required>
    </div>
    <div>
      <button type="submit" class="btn-valider">Déposer</button>
    </div>
  </form>
</div>
@endif

{{-- Liste des documents --}}
<div class="section-card">
  <div class="section-titre">Documents déposés</div>
  @if($documents->isEmpty())
    <div class="vide">Aucun document déposé pour le moment.</div>
  @else
    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Stage</th>
          <th>Déposé le</th>
        </tr>
      </thead>
      <tbody>
        @foreach($documents as $doc)
        <tr>
          <td>{{ $doc->nom_fichier }}</td>
          <td>{{ $doc->candidature->offre->titre ?? '—' }}</td>
          <td>{{ \Carbon\Carbon::parse($doc->date_depot)->format('d/m/Y') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
