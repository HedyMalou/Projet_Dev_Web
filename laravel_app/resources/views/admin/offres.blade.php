@extends('layouts.app')

@section('title', 'Gestion des offres')
@section('role-label', 'Administrateur')

@section('nav')
  <a href="{{ route('admin.dashboard') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Tableau de bord
  </a>
  <a href="{{ route('admin.offres') }}" class="nav-item active">
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
  <h1>Gestion des offres</h1>
  <p>Toutes les offres de stage publiées sur la plateforme.</p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif

<div class="section-card">
  @if($offres->isEmpty())
    <div class="vide">Aucune offre publiée pour le moment.</div>
  @else
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Entreprise</th>
          <th>Lieu</th>
          <th>Durée</th>
          <th>Candidatures</th>
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
          <td>{{ $offre->candidatures_count }}</td>
          <td>{{ \Carbon\Carbon::parse($offre->date_publication)->format('d/m/Y') }}</td>
          <td>
            <form method="POST" action="{{ route('admin.supprimer-offre') }}" style="display:inline;" onsubmit="return confirm('Supprimer cette offre ?')">
              @csrf
              <input type="hidden" name="id_offre" value="{{ $offre->id }}">
              <button type="submit" class="btn-supprimer">Supprimer</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
