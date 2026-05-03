<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Entreprise;
use App\Models\OffreStage;
use App\Models\Candidature;
use App\Models\Convention;
use App\Models\Document;
use App\Models\Commentaire;
use App\Models\Mission;

/**
 * Contrôleur de l'espace entreprise.
 *
 * Permet aux entreprises de publier des offres de stage, de consulter et
 * traiter les candidatures reçues, de valider les conventions, d'attribuer
 * des missions aux stagiaires et de dialoguer avec eux.
 */
class EntrepriseController extends Controller
{
    /**
     * Récupère l'entreprise liée à l'utilisateur courant en session.
     */
    private function getEntreprise(): Entreprise
    {
        return Entreprise::where('id_utilisateur', session('user_id'))->firstOrFail();
    }

    /**
     * Tableau de bord entreprise : KPI (offres publiées, candidatures
     * reçues, validées) et synthèse de l'activité récente.
     */
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

    public function mesOffres()
    {
        $entreprise = $this->getEntreprise();

        $offres = OffreStage::withCount('candidatures')
            ->where('id_entreprise', $entreprise->id)
            ->orderBy('date_publication','desc')
            ->get();

        return view('entreprise.mes_offres', compact('entreprise','offres'));
    }

    public function candidatureDetail($id)
    {
        $entreprise = $this->getEntreprise();

        $candidature = Candidature::with(['etudiant.utilisateur','offre','documents','convention','commentaires.utilisateur'])
            ->whereHas('offre', fn($q) => $q->where('id_entreprise', $entreprise->id))
            ->findOrFail($id);

        $missions = Mission::where('id_candidature', $candidature->id)
            ->orderBy('created_at','desc')->get();
        $candidature->setRelation('missions', $missions);

        return view('entreprise.candidature_detail', compact('entreprise','candidature'));
    }

    /**
     * Publication d'une nouvelle offre de stage : titre, description,
     * compétences requises, durée et lieu. La date de publication est
     * définie automatiquement.
     */
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

    public function supprimerOffre(Request $request)
    {
        $request->validate(['id_offre' => 'required|integer']);

        $entreprise = $this->getEntreprise();

        $offre = OffreStage::where('id_entreprise', $entreprise->id)->findOrFail($request->id_offre);
        $offre->delete();

        return redirect()->route('entreprise.offres')->with('succes', 'Offre supprimée.');
    }

    /**
     * Validation ou refus d'une candidature reçue. La validation déclenche
     * l'ouverture du workflow de convention pour cette candidature.
     */
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

        if ($request->statut === 'validee') {
            Convention::firstOrCreate(['id_candidature' => $candidature->id]);
        }

        return redirect()->route('entreprise.candidature', $candidature->id)
            ->with('succes', $request->statut === 'validee' ? 'Candidature validée.' : 'Candidature refusée.');
    }

    public function telechargerDocument(int $id)
    {
        $entreprise = $this->getEntreprise();

        $document = Document::whereHas('candidature.offre', fn($q) => $q->where('id_entreprise', $entreprise->id))
            ->findOrFail($id);

        return Storage::download($document->chemin_fichier, $document->nom_original ?? basename($document->chemin_fichier));
    }

    /**
     * Création d'une mission attribuée à un stagiaire. Restreinte aux
     * candidatures de l'entreprise et déjà validées.
     */
    public function creerMission(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'titre'          => 'required|string|max:200',
            'description'    => 'nullable|string|max:2000',
        ]);

        $entreprise = $this->getEntreprise();

        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('id_entreprise', $entreprise->id))
            ->where('statut', 'validee')
            ->findOrFail($request->id_candidature);

        Mission::create([
            'id_candidature' => $candidature->id,
            'titre'          => $request->titre,
            'description'    => $request->description,
            'statut'         => 'en_cours',
        ]);

        return redirect()->route('entreprise.candidature', $candidature->id)
            ->with('succes', 'Mission attribuée.');
    }

    public function terminerMission(int $id)
    {
        $entreprise = $this->getEntreprise();

        $mission = Mission::with('candidature.offre')->findOrFail($id);

        if (($mission->candidature->offre->id_entreprise ?? null) !== $entreprise->id) {
            abort(403);
        }

        $mission->update(['statut' => 'terminee']);

        return redirect()->route('entreprise.candidature', $mission->id_candidature)
            ->with('succes', 'Mission marquée comme terminée.');
    }

    public function commenter(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'contenu'        => 'required|string|max:2000',
        ]);

        $entreprise = $this->getEntreprise();

        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('id_entreprise', $entreprise->id))
            ->findOrFail($request->id_candidature);

        Commentaire::create([
            'id_candidature' => $candidature->id,
            'id_utilisateur' => session('user_id'),
            'contenu'        => $request->contenu,
        ]);

        return redirect()->route('entreprise.candidature', $candidature->id)
            ->with('succes', 'Remarque ajoutée.');
    }

    /**
     * Validation de la convention par l'entreprise. Étape parallèle à la
     * validation tuteur dans le workflow à 3 validateurs.
     */
    public function validerConvention(int $id)
    {
        $entreprise = $this->getEntreprise();

        $convention = Convention::with('candidature.offre')->findOrFail($id);

        if (($convention->candidature->offre->id_entreprise ?? null) !== $entreprise->id) {
            abort(403);
        }

        if ($convention->statut_etudiant !== 'signe') {
            return back()->with('erreur', 'L\'étudiant n\'a pas encore déposé la convention.');
        }

        $convention->update(['statut_entreprise' => 'signe']);

        return redirect()->route('entreprise.candidature', $convention->id_candidature)
            ->with('succes', 'Convention validée côté entreprise.');
    }

    public function telechargerConvention(int $id)
    {
        $entreprise = $this->getEntreprise();

        $convention = Convention::with('candidature.offre')->findOrFail($id);

        if (($convention->candidature->offre->id_entreprise ?? null) !== $entreprise->id || !$convention->chemin_fichier) {
            abort(403);
        }

        return Storage::download($convention->chemin_fichier, $convention->nom_original ?? basename($convention->chemin_fichier));
    }
}
