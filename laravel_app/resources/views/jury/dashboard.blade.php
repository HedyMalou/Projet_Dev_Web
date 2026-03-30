@extends('layouts.app')

@section('title', 'Tableau de bord jury')
@section('role-label', 'Jury')

@section('nav')
  <a href="{{ route('jury.dashboard') }}" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
    Évaluation stages
  </a>
@endsection

@section('content')
<div class="page-header">
  <h1>Évaluation des stages</h1>
  <p>Notez et commentez les stages validés.</p>
</div>

@if(session('succes'))
  <div class="alerte-succes">{{ session('succes') }}</div>
@endif

<div class="section-card">
  <div class="section-titre">Stages en cours</div>
  @if($candidatures->isEmpty())
    <div class="vide">Aucun stage validé à évaluer pour le moment.</div>
  @else
    <table>
      <thead>
        <tr>
          <th>Étudiant</th>
          <th>Stage</th>
          <th>Entreprise</th>
          <th>Note actuelle</th>
          <th>Attribuer une note</th>
          <th>Commentaire</th>
        </tr>
      </thead>
      <tbody>
        @foreach($candidatures as $c)
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
          <td>
            <form method="POST" action="{{ route('jury.noter') }}" style="display:flex;gap:6px;">
              @csrf
              <input type="hidden" name="id_candidature" value="{{ $c->id }}">
              <input type="number" name="note" class="form-control" style="width:70px;" min="0" max="20" step="0.5" placeholder="0–20" required>
              <button type="submit" class="btn-action">Valider</button>
            </form>
          </td>
          <td>
            <form method="POST" action="{{ route('jury.commenter') }}" style="display:flex;gap:6px;">
              @csrf
              <input type="hidden" name="id_candidature" value="{{ $c->id }}">
              <input type="text" name="contenu" class="form-control" style="width:160px;" placeholder="Votre commentaire…" required>
              <button type="submit" class="btn-action">Envoyer</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
