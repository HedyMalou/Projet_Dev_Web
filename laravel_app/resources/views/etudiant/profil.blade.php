@extends('layouts.app')

@section('title', 'Mon profil')
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
  <a href="{{ route('etudiant.documents') }}" class="nav-item">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
    Documents
  </a>
  <a href="{{ route('etudiant.profil') }}" class="nav-item active">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    Mon profil
  </a>
@endsection

@section('content')
<div class="page-header">
  <h1>Mon profil</h1>
</div>

<div class="kpi-grid-2">
  <div class="kpi-card">
    <div class="kpi-label">Candidatures</div>
    <div class="kpi-valeur">{{ $nb_cand }}</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Documents</div>
    <div class="kpi-valeur">{{ $nb_doc }}</div>
  </div>
</div>

<div class="section-card">
  <div class="section-titre">Informations personnelles</div>
  <table>
    <tbody>
      <tr><td style="color:var(--gris);width:180px;">Nom</td><td>{{ $etudiant->utilisateur->nom ?? '—' }}</td></tr>
      <tr><td style="color:var(--gris);">Prénom</td><td>{{ $etudiant->utilisateur->prenom ?? '—' }}</td></tr>
      <tr><td style="color:var(--gris);">Email</td><td>{{ $etudiant->utilisateur->email ?? '—' }}</td></tr>
      <tr><td style="color:var(--gris);">Numéro étudiant</td><td>{{ $etudiant->numero_etudiant ?? '—' }}</td></tr>
      <tr><td style="color:var(--gris);">Niveau</td><td>{{ $etudiant->niveau ?? '—' }}</td></tr>
      <tr><td style="color:var(--gris);">Spécialité</td><td>{{ $etudiant->specialite ?? '—' }}</td></tr>
    </tbody>
  </table>
</div>
@endsection
