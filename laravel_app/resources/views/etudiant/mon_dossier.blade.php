@extends('layouts.app')

@section('title', 'Mon dossier')
@section('role-label', 'Étudiant')

@section('nav')
  <a href="{{ route('etudiant.dashboard') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="{{ route('etudiant.dossier') }}" class="nav-item active">
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
  <h1>Mon dossier</h1>
  <p>Suivez vos candidatures et conventions.</p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif

<div class="section-card">
  <div class="section-titre">Mes candidatures</div>
  @if($candidatures->isEmpty())
    <div class="vide">Vous n'avez encore postulé à aucune offre.</div>
  @else
    <table>
      <thead>
        <tr>
          <th>Offre</th>
          <th>Entreprise</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Convention</th>
          <th>Documents</th>
        </tr>
      </thead>
      <tbody>
        @foreach($candidatures as $c)
        <tr>
          <td style="font-weight:500;">{{ $c->offre->titre ?? '—' }}</td>
          <td>{{ $c->offre->entreprise->nom ?? '—' }}</td>
          <td>{{ \Carbon\Carbon::parse($c->date_candidature)->format('d/m/Y') }}</td>
          <td>
            @if($c->statut === 'en_attente') <span class="badge-attente">En attente</span>
            @elseif($c->statut === 'validee')  <span class="badge-validee">Validée</span>
            @elseif($c->statut === 'refusee')  <span class="badge-refusee">Refusée</span>
            @else                              <span class="badge-archive">Archivée</span>
            @endif
          </td>
          <td>
            @if($c->convention)
              <span class="badge-validee">Signée</span>
            @else
              <span style="color:var(--gris);font-size:12px;">—</span>
            @endif
          </td>
          <td>
            {{ count($documents_par_cand[$c->id] ?? []) }} fichier(s)
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>

@if($commentaires->isNotEmpty())
<div class="section-card">
  <div class="section-titre">Commentaires reçus</div>
  @foreach($commentaires as $com)
  <div style="border-bottom:0.5px solid var(--bordure);padding:12px 0;last-child:border:none;">
    <div style="font-size:12px;color:var(--gris);margin-bottom:4px;">
      {{ $com->utilisateur->prenom ?? '' }} {{ $com->utilisateur->nom ?? '' }} — {{ \Carbon\Carbon::parse($com->date)->format('d/m/Y H:i') }}
    </div>
    <div style="font-size:14px;">{{ $com->contenu }}</div>
  </div>
  @endforeach
</div>
@endif
@endsection
