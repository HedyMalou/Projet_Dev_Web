@extends('layouts.app')

@section('title', 'Tableau de bord tuteur')
@section('role-label', 'Tuteur')

@section('nav')
  <a href="{{ route('tuteur.dashboard') }}" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Mes étudiants
  </a>
@endsection

@section('content')
<div class="page-header">
  <h1>Bonjour, {{ session('prenom') }}</h1>
  <p>Suivez et évaluez vos étudiants en stage.</p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif

<div class="kpi-grid-2">
  <div class="kpi-card">
    <div class="kpi-label">Étudiants suivis</div>
    <div class="kpi-valeur">{{ $nb_etudiants }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Conventions en attente</div>
    <div class="kpi-valeur">{{ $nb_conv_attente }}</div>
  </div>
</div>

<div class="section-card">
  <div class="section-titre">Étudiants en stage</div>
  @if($etudiants->isEmpty())
    <div class="vide">Aucun étudiant ne vous a encore été affecté.</div>
  @else
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Entreprise</th>
          <th>Note finale</th>
          <th>Commenter</th>
          <th>Noter</th>
        </tr>
      </thead>
      <tbody>
        @foreach($etudiants as $suivi)
        @php $c = $suivi->candidature; @endphp
        <tr>
          <td>{{ $c->etudiant->utilisateur->prenom ?? '' }} {{ $c->etudiant->utilisateur->nom ?? '' }}</td>
          <td>{{ $c->offre->titre ?? '—' }}</td>
          <td>{{ $c->offre->entreprise->nom ?? '—' }}</td>
          <td>
            @if($suivi->note_finale !== null)
              <span class="badge-note">{{ $suivi->note_finale }}/20</span>
            @else
              <span style="color:var(--gris);font-size:12px;">Non noté</span>
            @endif
          </td>
          <td>
            <form method="POST" action="{{ route('tuteur.commenter') }}" style="display:flex;gap:6px;">
              @csrf
              <input type="hidden" name="id_candidature" value="{{ $c->id }}">
              <input type="text" name="contenu" class="form-control" style="width:160px;" placeholder="Votre commentaire…" required>
              <button type="submit" class="btn-action">Envoyer</button>
            </form>
          </td>
          <td>
            <form method="POST" action="{{ route('tuteur.noter') }}" style="display:flex;gap:6px;">
              @csrf
              <input type="hidden" name="id_candidature" value="{{ $c->id }}">
              <input type="number" name="note" class="form-control" style="width:70px;" min="0" max="20" step="0.5" placeholder="0–20" required>
              <button type="submit" class="btn-action">Enregistrer</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
