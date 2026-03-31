<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\TuteurController;
use App\Http\Controllers\JuryController;
use App\Http\Controllers\AdminController;

// ── Accueil ───────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Authentification (accès public) ──────────────────────────────────────────
Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/register',        [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',       [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');

// A2F — accessible après login mais avant session complète
Route::get('/a2f',  [AuthController::class, 'showA2f'])->name('a2f');
Route::post('/a2f', [AuthController::class, 'verifyA2f']);

// Déconnexion
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Zone protégée (session requise) ───────────────────────────────────────────
Route::middleware('auth.check')->group(function () {

    // Redirige vers le bon dashboard selon le rôle
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Étudiant ──────────────────────────────────────────────────────────────
    Route::get('/etudiant/dashboard',           [EtudiantController::class, 'dashboard'])->name('etudiant.dashboard');
    Route::get('/etudiant/mon-dossier',         [EtudiantController::class, 'monDossier'])->name('etudiant.dossier');
    Route::get('/etudiant/documents',           [EtudiantController::class, 'documents'])->name('etudiant.documents');
    Route::get('/etudiant/profil',              [EtudiantController::class, 'profil'])->name('etudiant.profil');
    Route::post('/etudiant/postuler',                          [EtudiantController::class, 'postuler'])->name('etudiant.postuler');
    Route::get('/etudiant/documents/{id}/telecharger',         [EtudiantController::class, 'telechargerDocument'])->name('etudiant.documents.telecharger');
    Route::post('/etudiant/documents/upload',                  fn() => back()->with('erreur', 'Fonctionnalité à venir.'))->name('etudiant.documents.upload');

    // ── Entreprise ────────────────────────────────────────────────────────────
    Route::get('/entreprise/dashboard',            [EntrepriseController::class, 'dashboard'])->name('entreprise.dashboard');
    Route::get('/entreprise/mes-offres',           [EntrepriseController::class, 'mesOffres'])->name('entreprise.offres');
    Route::get('/entreprise/candidature/{id}',     [EntrepriseController::class, 'candidatureDetail'])->name('entreprise.candidature');
    Route::post('/entreprise/publier-offre',       [EntrepriseController::class, 'publierOffre'])->name('entreprise.publier');
    Route::post('/entreprise/supprimer-offre',     [EntrepriseController::class, 'supprimerOffre'])->name('entreprise.supprimer-offre');
    Route::post('/entreprise/valider-candidature', [EntrepriseController::class, 'validerCandidature'])->name('entreprise.valider');

    // ── Tuteur ────────────────────────────────────────────────────────────────
    Route::get('/tuteur/dashboard',  [TuteurController::class, 'dashboard'])->name('tuteur.dashboard');
    Route::post('/tuteur/commenter', [TuteurController::class, 'commenter'])->name('tuteur.commenter');
    Route::post('/tuteur/noter',     [TuteurController::class, 'noter'])->name('tuteur.noter');

    // ── Jury ──────────────────────────────────────────────────────────────────
    Route::get('/jury/dashboard',  [JuryController::class, 'dashboard'])->name('jury.dashboard');
    Route::post('/jury/noter',     [JuryController::class, 'noter'])->name('jury.noter');
    Route::post('/jury/commenter', [JuryController::class, 'commenter'])->name('jury.commenter');

    // ── Admin ─────────────────────────────────────────────────────────────────
    Route::get('/admin/dashboard',         [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/offres',            [AdminController::class, 'offres'])->name('admin.offres');
    Route::get('/admin/archivage',         [AdminController::class, 'archivage'])->name('admin.archivage');
    Route::post('/admin/supprimer-user',   [AdminController::class, 'supprimerUser'])->name('admin.supprimer-user');
    Route::post('/admin/supprimer-offre',  [AdminController::class, 'supprimerOffre'])->name('admin.supprimer-offre');
    Route::post('/admin/archiver',         [AdminController::class, 'archiver'])->name('admin.archiver');
    Route::post('/admin/valider-compte/{id}',  [AdminController::class, 'validerCompte'])->name('admin.valider-compte');
    Route::post('/admin/refuser-compte/{id}',  [AdminController::class, 'refuserCompte'])->name('admin.refuser-compte');
    Route::post('/admin/affecter-tuteur',      [AdminController::class, 'affecterTuteur'])->name('admin.affecter-tuteur');
});
