@extends('layouts.app')

@section('title', 'Détail candidature')
@section('role-label', 'Entreprise')

@section('nav')
  <a href="{{ route('entreprise.dashboard') }}" class="nav-item">
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
  <h1>Candidature — {{ $candidature->offre->titre ?? '—' }}</h1>
  <p><a href="{{ route('entreprise.dashboard') }}" style="color:var(--bleu-clair);">← Retour</a></p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif

<div class="section-card">
  <div class="section-titre">Informations étudiant</div>
  <table>
    <tbody>
      <tr><td style="color:var(--gris);width:180px;">Nom</td><td>{{ $candidature->etudiant->utilisateur->nom ?? '—' }}</td></tr>
      <tr><td style="color:var(--gris);">Prénom</td><td>{{ $candidature->etudiant->utilisateur->prenom ?? '—' }}</td></tr>
      <tr><td style="color:var(--gris);">Email</td><td>{{ $candidature->etudiant->utilisateur->email ?? '—' }}</td></tr>
      <tr>
        <td style="color:var(--gris);">Statut</td>
        <td>
          @if($candidature->statut === 'en_attente') <span class="badge-attente">En attente</span>
          @elseif($candidature->statut === 'validee')  <span class="badge-validee">Validée</span>
          @elseif($candidature->statut === 'refusee')  <span class="badge-refusee">Refusée</span>
          @else                                        <span class="badge-archive">Archivée</span>
          @endif
        </td>
      </tr>
      <tr><td style="color:var(--gris);">Date candidature</td><td>{{ \Carbon\Carbon::parse($candidature->date_candidature)->format('d/m/Y') }}</td></tr>
    </tbody>
  </table>
</div>

@if($candidature->documents->isNotEmpty())
<div class="section-card">
  <div class="section-titre">Documents fournis</div>
  <table>
    <thead><tr><th>Nom du fichier</th><th>Déposé le</th></tr></thead>
    <tbody>
      @foreach($candidature->documents as $doc)
      <tr>
        <td>{{ $doc->nom_fichier }}</td>
        <td>{{ \Carbon\Carbon::parse($doc->date_depot)->format('d/m/Y') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

@if($candidature->statut === 'en_attente')
<div class="section-card">
  <div class="section-titre">Décision</div>
  <form method="POST" action="{{ route('entreprise.valider') }}" style="display:flex;gap:12px;">
    @csrf
    <input type="hidden" name="id_candidature" value="{{ $candidature->id }}">
    <button type="submit" name="statut" value="validee" class="btn-valider">Valider la candidature</button>
    <button type="submit" name="statut" value="refusee" class="btn-supprimer" style="padding:9px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Refuser</button>
  </form>
</div>
@endif
@endsection
