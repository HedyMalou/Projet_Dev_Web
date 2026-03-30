<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entreprise;
use App\Models\OffreStage;
use App\Models\Candidature;
use App\Models\Convention;

class EntrepriseController extends Controller
{
    private function getEntreprise(): Entreprise
    {
        return Entreprise::where('id_utilisateur', session('user_id'))->firstOrFail();
    }

    // ── Tableau de bord ───────────────────────────────────────────────────────

    public function dashboard()
    {
        $entreprise = $this->getEntreprise();

        $offres = OffreStage::where('id_entreprise', $entreprise->id)
            ->orderBy('date_publication','desc')->get();

        $candidatures = Candidature::with(['etudiant.utilisateur','offre'])
            ->whereHas('offre', fn($q) => $q->where('id_entreprise', $entreprise->id))
            ->orderBy('date_candidature','desc')
            ->get();

        $nb_offres       = $offres->count();
        $nb_candidatures = $candidatures->count();
        $nb_en_attente   = $candidatures->where('statut','en_attente')->count();

        return view('entreprise.dashboard', compact('entreprise','offres','candidatures','nb_offres','nb_candidatures','nb_en_attente'));
    }

    // ── Mes offres ────────────────────────────────────────────────────────────

    public function mesOffres()
    {
        $entreprise = $this->getEntreprise();

        $offres = OffreStage::withCount('candidatures')
            ->where('id_entreprise', $entreprise->id)
            ->orderBy('date_publication','desc')
            ->get();

        return view('entreprise.mes_offres', compact('entreprise','offres'));
    }

    // ── Détail candidature ────────────────────────────────────────────────────

    public function candidatureDetail($id)
    {
        $entreprise = $this->getEntreprise();

        $candidature = Candidature::with(['etudiant.utilisateur','offre','documents'])
            ->whereHas('offre', fn($q) => $q->where('id_entreprise', $entreprise->id))
            ->findOrFail($id);

        return view('entreprise.candidature_detail', compact('entreprise','candidature'));
    }

    // ── Publier une offre ─────────────────────────────────────────────────────

    public function publierOffre(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:200',
            'duree' => 'required|string',
            'lieu'  => 'required|string|max:150',
        ]);

        $entreprise = $this->getEntreprise();

        OffreStage::create([
            'id_entreprise' => $entreprise->id,
            'titre'         => $request->titre,
            'description'   => $request->description,
            'competences'   => $request->competences,
            'duree'         => $request->duree,
            'lieu'          => $request->lieu,
        ]);

        return redirect()->route('entreprise.dashboard')->with('succes', 'Offre publiée avec succès !');
    }

    // ── Supprimer une offre ───────────────────────────────────────────────────

    public function supprimerOffre(Request $request)
    {
        $request->validate(['id_offre' => 'required|integer']);

        $entreprise = $this->getEntreprise();

        $offre = OffreStage::where('id_entreprise', $entreprise->id)->findOrFail($request->id_offre);
        $offre->delete();

        return redirect()->route('entreprise.offres')->with('succes', 'Offre supprimée.');
    }

    // ── Valider / refuser une candidature ─────────────────────────────────────

    public function validerCandidature(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer',
            'statut'         => 'required|in:validee,refusee',
        ]);

        $entreprise = $this->getEntreprise();

        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('id_entreprise', $entreprise->id))
            ->findOrFail($request->id_candidature);

        $candidature->update(['statut' => $request->statut]);

        // Si validée → créer la convention
        if ($request->statut === 'validee') {
            Convention::firstOrCreate(['id_candidature' => $candidature->id]);
        }

        return redirect()->route('entreprise.candidature', $candidature->id)
            ->with('succes', $request->statut === 'validee' ? 'Candidature validée.' : 'Candidature refusée.');
    }
}
