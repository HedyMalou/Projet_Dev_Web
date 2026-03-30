@extends('layouts.app')

@section('title', 'Archivage')
@section('role-label', 'Administrateur')

@section('nav')
  <a href="{{ route('admin.dashboard') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="{{ route('admin.offres') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Offres
  </a>
  <a href="{{ route('admin.archivage') }}" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
    Archivage
  </a>
@endsection

@section('content')
<div class="page-header">
  <h1>Archivage des stages</h1>
  <p>Archivez les dossiers de stages terminés.</p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif

{{-- Stages à archiver --}}
<div class="section-card">
  <div class="section-titre">Stages validés — à archiver</div>
  @if($stages_valides->isEmpty())
    <div class="vide">Aucun stage validé en attente d'archivage.</div>
  @else
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Entreprise</th>
          <th>Tuteur</th>
          <th>Note</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($stages_valides as $c)
        <tr>
          <td>{{ $c->etudiant->utilisateur->prenom ?? '' }} {{ $c->etudiant->utilisateur->nom ?? '' }}</td>
          <td>{{ $c->offre->titre ?? '—' }}</td>
          <td>{{ $c->offre->entreprise->nom ?? '—' }}</td>
          <td>
            @if($c->suivi && $c->suivi->tuteur)
              {{ $c->suivi->tuteur->utilisateur->prenom ?? '' }} {{ $c->suivi->tuteur->utilisateur->nom ?? '' }}
            @else
              <span style="color:var(--gris);">Non affecté</span>
            @endif
          </td>
          <td>
            @if($c->suivi && $c->suivi->note_finale !== null)
              <span class="badge-note">{{ $c->suivi->note_finale }}/20</span>
            @else
              <span style="color:var(--gris);font-size:12px;">—</span>
            @endif
          </td>
          <td>
            <form method="POST" action="{{ route('admin.archiver') }}" style="display:inline;">
              @csrf
              <input type="hidden" name="id_candidature" value="{{ $c->id }}">
              <button type="submit" class="btn-action">Archiver</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>

{{-- Archives --}}
<div class="section-card">
  <div class="section-titre">Dossiers archivés</div>
  @if($archives->isEmpty())
    <div class="vide">Aucun dossier archivé.</div>
  @else
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Entreprise</th>
          <th>Note finale</th>
        </tr>
      </thead>
      <tbody>
        @foreach($archives as $c)
        <tr>
          <td>{{ $c->etudiant->utilisateur->prenom ?? '' }} {{ $c->etudiant->utilisateur->nom ?? '' }}</td>
          <td>{{ $c->offre->titre ?? '—' }}</td>
          <td>{{ $c->offre->entreprise->nom ?? '—' }}</td>
          <td>
            @if($c->suivi && $c->suivi->note_finale !== null)
              <span class="badge-note">{{ $c->suivi->note_finale }}/20</span>
            @else
              <span style="color:var(--gris);font-size:12px;">—</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
