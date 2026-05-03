<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\TuteurController;
use App\Http\Controllers\JuryController;
use App\Http\Controllers\AdminController;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/register',        [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',       [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');

Route::get('/a2f',  [AuthController::class, 'showA2f'])->name('a2f');
Route::post('/a2f', [AuthController::class, 'verifyA2f']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth.check')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/utilisateur/{id}', [DashboardController::class, 'profilPublic'])->name('utilisateur.profil');

    Route::get('/etudiant/dashboard',           [EtudiantController::class, 'dashboard'])->name('etudiant.dashboard');
    Route::get('/etudiant/mon-dossier',         [EtudiantController::class, 'monDossier'])->name('etudiant.dossier');
    Route::get('/etudiant/documents',           [EtudiantController::class, 'documents'])->name('etudiant.documents');
    Route::get('/etudiant/profil',              [EtudiantController::class, 'profil'])->name('etudiant.profil');
    Route::post('/etudiant/profil',             [EtudiantController::class, 'updateProfil'])->name('etudiant.profil.update');
    Route::get('/etudiant/demande-formation',   [EtudiantController::class, 'demandeFormation'])->name('etudiant.demande-formation');
    Route::post('/etudiant/demande-formation',  [EtudiantController::class, 'soumettreDemandeFormation'])->name('etudiant.demande-formation.soumettre');
    Route::post('/etudiant/postuler',                          [EtudiantController::class, 'postuler'])->name('etudiant.postuler');
    Route::post('/etudiant/candidatures/{id}/annuler',          [EtudiantController::class, 'annulerCandidature'])->name('etudiant.annuler-candidature');
    Route::get('/etudiant/documents/{id}/telecharger',         [EtudiantController::class, 'telechargerDocument'])->name('etudiant.documents.telecharger');
    Route::post('/etudiant/documents/upload',                  [EtudiantController::class, 'uploadDocument'])->name('etudiant.documents.upload');
    Route::post('/etudiant/convention/upload',                 [EtudiantController::class, 'uploadConvention'])->name('etudiant.convention.upload');
    Route::post('/etudiant/commenter',                          [EtudiantController::class, 'commenter'])->name('etudiant.commenter');
    Route::get('/etudiant/cahier',                              [EtudiantController::class, 'cahier'])->name('etudiant.cahier');
    Route::post('/etudiant/cahier',                             [EtudiantController::class, 'ajouterEntreeCahier'])->name('etudiant.cahier.ajouter');
    Route::get('/etudiant/convention/{id}/telecharger',        [EtudiantController::class, 'telechargerConvention'])->name('etudiant.convention.telecharger');

    Route::get('/entreprise/dashboard',            [EntrepriseController::class, 'dashboard'])->name('entreprise.dashboard');
    Route::get('/entreprise/mes-offres',           [EntrepriseController::class, 'mesOffres'])->name('entreprise.offres');
    Route::get('/entreprise/candidature/{id}',     [EntrepriseController::class, 'candidatureDetail'])->name('entreprise.candidature');
    Route::get('/entreprise/documents/{id}/telecharger', [EntrepriseController::class, 'telechargerDocument'])->name('entreprise.documents.telecharger');
    Route::post('/entreprise/publier-offre',       [EntrepriseController::class, 'publierOffre'])->name('entreprise.publier');
    Route::post('/entreprise/supprimer-offre',     [EntrepriseController::class, 'supprimerOffre'])->name('entreprise.supprimer-offre');
    Route::post('/entreprise/valider-candidature', [EntrepriseController::class, 'validerCandidature'])->name('entreprise.valider');
    Route::post('/entreprise/commenter',           [EntrepriseController::class, 'commenter'])->name('entreprise.commenter');
    Route::post('/entreprise/missions',            [EntrepriseController::class, 'creerMission'])->name('entreprise.mission.creer');
    Route::post('/entreprise/missions/{id}/terminer', [EntrepriseController::class, 'terminerMission'])->name('entreprise.mission.terminer');
    Route::post('/entreprise/convention/{id}/valider', [EntrepriseController::class, 'validerConvention'])->name('entreprise.convention.valider');
    Route::get('/entreprise/convention/{id}/telecharger', [EntrepriseController::class, 'telechargerConvention'])->name('entreprise.convention.telecharger');

    Route::get('/tuteur/dashboard',  [TuteurController::class, 'dashboard'])->name('tuteur.dashboard');
    Route::get('/tuteur/offres',     [TuteurController::class, 'offres'])->name('tuteur.offres');
    Route::get('/tuteur/demandes',   [TuteurController::class, 'demandes'])->name('tuteur.demandes');
    Route::post('/tuteur/demandes',  [TuteurController::class, 'soumettreDemande'])->name('tuteur.soumettre-demande');
    Route::post('/tuteur/commenter', [TuteurController::class, 'commenter'])->name('tuteur.commenter');
    Route::post('/tuteur/noter',     [TuteurController::class, 'noter'])->name('tuteur.noter');
    Route::post('/tuteur/convention/{id}/valider', [TuteurController::class, 'validerConvention'])->name('tuteur.convention.valider');
    Route::get('/tuteur/convention/{id}/telecharger', [TuteurController::class, 'telechargerConvention'])->name('tuteur.convention.telecharger');
    Route::get('/tuteur/cahier/{id_candidature}', [TuteurController::class, 'voirCahier'])->name('tuteur.cahier');

    Route::get('/jury/dashboard',  [JuryController::class, 'dashboard'])->name('jury.dashboard');
    Route::get('/jury/cahier/{id_candidature}', [JuryController::class, 'cahier'])->name('jury.cahier');
    Route::post('/jury/noter',     [JuryController::class, 'noter'])->name('jury.noter');
    Route::post('/jury/commenter', [JuryController::class, 'commenter'])->name('jury.commenter');

    Route::get('/admin/dashboard',         [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/offres',            [AdminController::class, 'offres'])->name('admin.offres');
    Route::get('/admin/archivage',         [AdminController::class, 'archivage'])->name('admin.archivage');
    Route::get('/admin/encadrement',       [AdminController::class, 'encadrement'])->name('admin.encadrement');
    Route::post('/admin/supprimer-user',   [AdminController::class, 'supprimerUser'])->name('admin.supprimer-user');
    Route::post('/admin/users/{id}/role',  [AdminController::class, 'modifierRole'])->name('admin.modifier-role');
    Route::post('/admin/supprimer-offre',  [AdminController::class, 'supprimerOffre'])->name('admin.supprimer-offre');
    Route::post('/admin/archiver',         [AdminController::class, 'archiver'])->name('admin.archiver');
    Route::post('/admin/desarchiver/{id}', [AdminController::class, 'desarchiver'])->name('admin.desarchiver');
    Route::post('/admin/valider-compte/{id}',  [AdminController::class, 'validerCompte'])->name('admin.valider-compte');
    Route::post('/admin/refuser-compte/{id}',  [AdminController::class, 'refuserCompte'])->name('admin.refuser-compte');
    Route::post('/admin/affecter-tuteur',      [AdminController::class, 'affecterTuteur'])->name('admin.affecter-tuteur');
    Route::post('/admin/encadrement/accepter/{id}',  [AdminController::class, 'accepterDemande'])->name('admin.accepter-demande');
    Route::post('/admin/encadrement/refuser/{id}',   [AdminController::class, 'refuserDemande'])->name('admin.refuser-demande');
    Route::post('/admin/encadrement/affecter-jury',  [AdminController::class, 'affecterJury'])->name('admin.affecter-jury');
    Route::post('/admin/encadrement/retirer-jury/{id}', [AdminController::class, 'retirerJury'])->name('admin.retirer-jury');
    Route::post('/admin/convention/{id}/valider', [AdminController::class, 'validerConvention'])->name('admin.convention.valider');
    Route::get('/admin/convention/{id}/telecharger', [AdminController::class, 'telechargerConvention'])->name('admin.convention.telecharger');
    Route::get('/admin/demandes-formation',                  [AdminController::class, 'demandesFormation'])->name('admin.demandes-formation');
    Route::post('/admin/demandes-formation/{id}/valider',    [AdminController::class, 'validerDemandeFormation'])->name('admin.demandes-formation.valider');
    Route::post('/admin/demandes-formation/{id}/refuser',    [AdminController::class, 'refuserDemandeFormation'])->name('admin.demandes-formation.refuser');
});
