<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Etudiant;
use App\Models\Tuteur;
use App\Models\OffreStage;
use App\Models\Candidature;
use App\Models\Suivi;

class AdminController extends Controller
{
    // ── Tableau de bord ───────────────────────────────────────────────────────

    public function dashboard()
    {
        $admin = Utilisateur::findOrFail(session('user_id'));

        $nb_etudiants = Etudiant::count();
        $nb_offres    = OffreStage::count();
        $nb_stages    = Candidature::where('statut','validee')->count();
        $nb_users     = Utilisateur::count();

        $users = Utilisateur::orderBy('created_at','desc')->get();

        return view('admin.dashboard', compact('admin','nb_etudiants','nb_offres','nb_stages','nb_users','users'));
    }

    // ── Gestion utilisateurs ──────────────────────────────────────────────────

    public function supprimerUser(Request $request)
    {
        $request->validate(['supprimer_id' => 'required|integer']);

        $id = (int)$request->supprimer_id;

        // Ne peut pas supprimer son propre compte
        if ($id === (int)session('user_id')) {
            return redirect()->route('admin.dashboard')->with('erreur', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        Utilisateur::findOrFail($id)->delete();

        return redirect()->route('admin.dashboard')->with('succes', 'Utilisateur supprimé.');
    }

    // ── Gestion offres ────────────────────────────────────────────────────────

    public function offres()
    {
        $admin = Utilisateur::findOrFail(session('user_id'));

        $offres = OffreStage::with('entreprise')
            ->withCount('candidatures')
            ->orderBy('date_publication','desc')
            ->get();

        return view('admin.offres', compact('admin','offres'));
    }

    public function supprimerOffre(Request $request)
    {
        $request->validate(['id_offre' => 'required|integer']);

        OffreStage::findOrFail($request->id_offre)->delete();

        return redirect()->route('admin.offres')->with('succes', 'Offre supprimée.');
    }

    // ── Affecter tuteur ───────────────────────────────────────────────────────

    public function affecterTuteur(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'id_tuteur'      => 'required|integer|exists:TUTEUR,id',
        ]);

        $candidature = Candidature::where('statut','validee')->findOrFail($request->id_candidature);

        Suivi::updateOrCreate(
            ['id_candidature' => $candidature->id],
            ['id_tuteur'      => $request->id_tuteur]
        );

        return redirect()->route('admin.dashboard')->with('succes', 'Tuteur affecté.');
    }

    // ── Archivage ─────────────────────────────────────────────────────────────

    public function archivage()
    {
        $admin = Utilisateur::findOrFail(session('user_id'));

        $stages_valides = Candidature::with(['etudiant.utilisateur','offre.entreprise','suivi'])
            ->where('statut','validee')
            ->orderBy('date_candidature','desc')
            ->get();

        $archives = Candidature::with(['etudiant.utilisateur','offre.entreprise','suivi'])
            ->where('statut','archivee')
            ->orderBy('date_candidature','desc')
            ->get();

        return view('admin.archivage', compact('admin','stages_valides','archives'));
    }

    public function archiver(Request $request)
    {
        $request->validate(['id_candidature' => 'required|integer']);

        $candidature = Candidature::where('statut','validee')->findOrFail($request->id_candidature);
        $candidature->update(['statut' => 'archivee']);

        return redirect()->route('admin.archivage')->with('succes', 'Dossier archivé.');
    }
}
