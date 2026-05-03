<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Etudiant;
use App\Models\Candidature;
use App\Models\Document;
use App\Models\Commentaire;
use App\Models\Convention;
use App\Models\CahierStage;
use App\Models\OffreStage;
use App\Models\Utilisateur;
use App\Models\DemandeFormation;

/**
 * Contrôleur de l'espace étudiant.
 *
 * Regroupe l'ensemble des actions accessibles à un utilisateur connecté
 * avec le rôle "etudiant" : recherche d'offres, gestion des candidatures,
 * dépôt de documents, cahier de stage, conventions et profil.
 */
class EtudiantController extends Controller
{
    /**
     * Récupère l'étudiant lié à l'utilisateur courant en session.
     * Lève une 404 si aucun profil étudiant n'est associé.
     */
    private function getEtudiant(): Etudiant
    {
        return Etudiant::where('id_utilisateur', session('user_id'))->firstOrFail();
    }

    /**
     * Tableau de bord étudiant : KPI personnels, moteur de recherche
     * d'offres avec filtres (mot-clé, durée, lieu, filière) et liste
     * des candidatures déjà déposées sur ces offres.
     */
    public function dashboard(Request $request)
    {
        $etudiant = $this->getEtudiant();

        $nb_candidatures = Candidature::where('id_etudiant', $etudiant->id)->count();
        $nb_documents    = Document::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))->count();

        $q       = $request->input('q', '');
        $duree   = $request->input('duree', '');
        $lieu    = $request->input('lieu', '');
        $filiere = $request->input('filiere', '');

        $query = OffreStage::with('entreprise');
        if ($q)       $query->where(fn($sq) => $sq->where('titre','like',"%$q%")->orWhere('competences','like',"%$q%")->orWhere('description','like',"%$q%"));
        if ($duree)   $query->where('duree', $duree);
        if ($lieu)    $query->where('lieu', 'like', "%$lieu%");
        if ($filiere) $query->where(fn($sq) => $sq->where('titre','like',"%$filiere%")->orWhere('competences','like',"%$filiere%")->orWhere('description','like',"%$filiere%"));

        $offres = $query->orderBy('date_publication', 'desc')->limit(6)->get();

        $candidatures_par_offre = Candidature::where('id_etudiant', $etudiant->id)
            ->get()
            ->keyBy('id_offre');

        return view('etudiant.dashboard', compact('etudiant','nb_candidatures','nb_documents','offres','candidatures_par_offre','q','duree','lieu','filiere'));
    }

    /**
     * "Mon dossier" : agrège candidatures, conventions, missions, documents
     * et commentaires de l'étudiant pour une vue unifiée du dossier de stage.
     */
    public function monDossier()
    {
        $etudiant = $this->getEtudiant();

        $candidatures = Candidature::with(['offre.entreprise','convention','missions'])
            ->where('id_etudiant', $etudiant->id)
            ->orderBy('date_candidature', 'desc')
            ->get();

        $tous_docs = Document::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))
            ->orderBy('date_depot','desc')->get();

        $documents_par_cand = [];
        foreach ($tous_docs as $doc) {
            $documents_par_cand[$doc->id_candidature][] = $doc;
        }

        $commentaires = Commentaire::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))
            ->with('utilisateur')
            ->orderBy('date','desc')
            ->get();

        return view('etudiant.mon_dossier', compact('etudiant','candidatures','documents_par_cand','commentaires'));
    }

    /**
     * Liste les documents déposés par candidature. Le dépôt de documents
     * de restitution (rapport, résumé, fiche d'évaluation) n'est ouvert
     * que sur les candidatures validées ou archivées.
     */
    public function documents()
    {
        $etudiant = $this->getEtudiant();

        $candidatures = Candidature::with(['offre.entreprise', 'documents' => fn($q) => $q->orderBy('date_depot','desc')])
            ->where('id_etudiant', $etudiant->id)
            ->orderBy('date_candidature','desc')
            ->get();

        $candidatures_valides = $candidatures->whereIn('statut',['validee','archivee']);

        return view('etudiant.documents', compact('etudiant','candidatures','candidatures_valides'));
    }

    /**
     * Affiche la page de profil de l'étudiant avec ses statistiques personnelles.
     */
    public function profil()
    {
        $etudiant = Etudiant::with('utilisateur')
            ->where('id_utilisateur', session('user_id'))
            ->firstOrFail();

        $nb_cand = Candidature::where('id_etudiant', $etudiant->id)->count();
        $nb_doc  = Document::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))->count();

        return view('etudiant.profil', compact('etudiant','nb_cand','nb_doc'));
    }

    /**
     * Met à jour les informations modifiables du profil : email, filière,
     * promotion et mot de passe. Les nom, prénom et numéro étudiant sont
     * verrouillés et ne peuvent être modifiés que par l'administrateur.
     */
    public function updateProfil(Request $request)
    {
        $etudiant = $this->getEtudiant();
        $user     = $etudiant->utilisateur;

        $request->validate([
            'email'        => 'required|email|unique:UTILISATEUR,email,'.$user->id,
            'filiere'      => 'required|string|max:100',
            'promotion'    => 'required|string|max:10',
            'mot_de_passe' => 'nullable|min:8|confirmed',
        ]);

        $user->update([
            'email'  => $request->email,
        ]);

        if ($request->filled('mot_de_passe')) {
            $user->update(['mot_de_passe' => \Illuminate\Support\Facades\Hash::make($request->mot_de_passe)]);
        }

        $etudiant->update([
            'filiere'   => $request->filiere,
            'promotion' => $request->promotion,
        ]);

        return redirect()->route('etudiant.profil')->with('succes', 'Profil mis à jour.');
    }

    /**
     * Affiche le formulaire de demande d'ajout de formation
     * et l'historique des demandes de l'étudiant connecté.
     */
    public function demandeFormation()
    {
        $etudiant = $this->getEtudiant();

        $demandes = DemandeFormation::where('id_etudiant', $etudiant->id)
            ->orderBy('date_demande', 'desc')
            ->get();

        return view('etudiant.demande_formation', compact('etudiant', 'demandes'));
    }

    /**
     * Enregistre une nouvelle demande d'ajout de formation.
     * La demande sera traitée par l'administrateur (statut initial : en_attente).
     */
    public function soumettreDemandeFormation(Request $request)
    {
        $request->validate([
            'nom_formation' => 'required|string|max:100',
            'description'   => 'nullable|string|max:1000',
        ]);

        $etudiant = $this->getEtudiant();

        DemandeFormation::create([
            'id_etudiant'   => $etudiant->id,
            'nom_formation' => $request->nom_formation,
            'description'   => $request->description,
            'statut'        => 'en_attente',
            'date_demande'  => now(),
        ]);

        return redirect()->route('etudiant.demande-formation')
            ->with('succes', 'Votre demande a été envoyée à l\'administrateur.');
    }

    /**
     * Postuler à une offre : crée la candidature et stocke en parallèle
     * le CV et la lettre de motivation comme documents associés.
     * Empêche les doublons (un étudiant ne peut postuler qu'une fois par offre).
     */
    public function postuler(Request $request)
    {
        $request->validate([
            'id_offre'          => 'required|integer|exists:OFFRE_STAGE,id',
            'cv'                => 'required|file|mimes:pdf,doc,docx|max:5120',
            'lettre_motivation' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $etudiant = $this->getEtudiant();

        $dejaPostule = Candidature::where('id_etudiant', $etudiant->id)
            ->where('id_offre', $request->id_offre)
            ->exists();

        if ($dejaPostule) {
            return redirect()->route('etudiant.dashboard')->with('erreur', 'Vous avez déjà postulé à cette offre.');
        }

        $candidature = Candidature::create([
            'id_etudiant' => $etudiant->id,
            'id_offre'    => $request->id_offre,
        ]);

        // Stockage des deux fichiers obligatoires de la candidature
        foreach (['cv', 'lettre_motivation'] as $type) {
            $fichier = $request->file($type);
            $chemin  = $fichier->store('uploads');
            Document::create([
                'id_candidature' => $candidature->id,
                'type'           => $type,
                'chemin_fichier' => $chemin,
                'nom_original'   => $fichier->getClientOriginalName(),
            ]);
        }

        return redirect()->route('etudiant.dashboard')->with('succes', 'Candidature envoyée avec vos documents !');
    }

    /**
     * Télécharge un document. La jointure whereHas garantit que l'étudiant
     * ne peut télécharger que ses propres documents (vérification de propriété).
     */
    public function telechargerDocument(int $id)
    {
        $etudiant = $this->getEtudiant();

        $document = Document::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))
            ->findOrFail($id);

        return Storage::download($document->chemin_fichier, $document->nom_original ?? basename($document->chemin_fichier));
    }

    /**
     * Ajoute un commentaire de l'étudiant sur l'une de ses candidatures.
     * Permet la communication bidirectionnelle avec l'entreprise et le tuteur.
     */
    public function commenter(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'contenu'        => 'required|string|max:2000',
        ]);

        $etudiant = $this->getEtudiant();

        Candidature::where('id_etudiant', $etudiant->id)->findOrFail($request->id_candidature);

        Commentaire::create([
            'id_candidature' => $request->id_candidature,
            'id_utilisateur' => session('user_id'),
            'contenu'        => $request->contenu,
        ]);

        return redirect()->route('etudiant.dossier')->with('succes', 'Message envoyé.');
    }

    /**
     * Dépôt d'un document de restitution (rapport, résumé, fiche d'évaluation).
     * Restreint aux candidatures dont le statut est validee ou archivee :
     * un étudiant ne peut déposer ces livrables que sur un stage déjà accepté.
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'type'           => 'required|in:rapport,resume,fiche_evaluation,autre',
            'fichier'        => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $etudiant = $this->getEtudiant();

        $candidature = Candidature::where('id_etudiant', $etudiant->id)
            ->whereIn('statut', ['validee','archivee'])
            ->findOrFail($request->id_candidature);

        $fichier = $request->file('fichier');
        $chemin  = $fichier->store('uploads');

        Document::create([
            'id_candidature' => $candidature->id,
            'type'           => $request->type,
            'chemin_fichier' => $chemin,
            'nom_original'   => $fichier->getClientOriginalName(),
        ]);

        return redirect()->route('etudiant.documents')->with('succes', 'Document déposé.');
    }

    /**
     * Dépôt de la convention signée par l'étudiant. Première étape du
     * workflow à 3 validateurs : entreprise et tuteur valident en parallèle,
     * puis l'administrateur effectue la validation finale.
     */
    public function uploadConvention(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'fichier'        => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $etudiant = $this->getEtudiant();

        $candidature = Candidature::where('id_etudiant', $etudiant->id)
            ->where('statut', 'validee')
            ->findOrFail($request->id_candidature);

        $fichier = $request->file('fichier');
        $chemin  = $fichier->store('conventions');

        Convention::updateOrCreate(
            ['id_candidature' => $candidature->id],
            [
                'chemin_fichier'  => $chemin,
                'nom_original'   => $fichier->getClientOriginalName(),
                'statut_etudiant' => 'signe',
            ]
        );

        return redirect()->route('etudiant.dossier')->with('succes', 'Convention déposée. En attente de validation.');
    }

    public function telechargerConvention(int $id)
    {
        $etudiant = $this->getEtudiant();

        $convention = Convention::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))
            ->findOrFail($id);

        if (!$convention->chemin_fichier) {
            abort(404);
        }

        return Storage::download($convention->chemin_fichier, $convention->nom_original ?? basename($convention->chemin_fichier));
    }

    /**
     * Cahier de stage : liste les entrées journalières par candidature
     * pour les stages validés ou archivés.
     */
    public function cahier()
    {
        $etudiant = $this->getEtudiant();

        $candidatures_actives = Candidature::with('offre.entreprise')
            ->where('id_etudiant', $etudiant->id)
            ->whereIn('statut', ['validee','archivee'])
            ->orderBy('date_candidature','desc')
            ->get();

        $entrees = CahierStage::whereIn('id_candidature', $candidatures_actives->pluck('id'))
            ->orderBy('date_jour','desc')
            ->orderBy('created_at','desc')
            ->get()
            ->groupBy('id_candidature');

        return view('etudiant.cahier', compact('etudiant','candidatures_actives','entrees'));
    }

    public function ajouterEntreeCahier(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'date_jour'      => 'required|date',
            'contenu'        => 'required|string|max:5000',
        ]);

        $etudiant = $this->getEtudiant();

        Candidature::where('id_etudiant', $etudiant->id)
            ->whereIn('statut', ['validee','archivee'])
            ->findOrFail($request->id_candidature);

        CahierStage::create([
            'id_candidature' => $request->id_candidature,
            'date_jour'      => $request->date_jour,
            'contenu'        => $request->contenu,
        ]);

        return redirect()->route('etudiant.cahier')->with('succes', 'Entrée ajoutée au cahier de stage.');
    }

    /**
     * Annulation d'une candidature : autorisée uniquement si elle est
     * encore en attente. Supprime également les fichiers associés du disque.
     */
    public function annulerCandidature(int $id)
    {
        $etudiant = $this->getEtudiant();

        $candidature = Candidature::where('id_etudiant', $etudiant->id)->findOrFail($id);

        if ($candidature->statut !== 'en_attente') {
            return redirect()->route('etudiant.dossier')
                ->with('erreur', 'Seules les candidatures en attente peuvent être annulées.');
        }

        foreach ($candidature->documents as $doc) {
            Storage::delete($doc->chemin_fichier);
        }

        $candidature->delete();

        return redirect()->route('etudiant.dossier')->with('succes', 'Candidature annulée.');
    }
}
