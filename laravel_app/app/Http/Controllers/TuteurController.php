<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Tuteur;
use App\Models\Suivi;
use App\Models\Commentaire;
use App\Models\Candidature;
use App\Models\Convention;
use App\Models\CahierStage;
use App\Models\DemandeEncadrement;
use App\Models\OffreStage;

/**
 * Contrôleur de l'espace tuteur pédagogique.
 *
 * Le tuteur encadre des étudiants en stage : il peut consulter leur cahier
 * de stage, valider leur convention, attribuer des notes et dialoguer avec
 * eux via les commentaires. Il soumet à l'administrateur ses demandes
 * d'encadrement pour être affecté à de nouveaux étudiants.
 */
class TuteurController extends Controller
{
    /**
     * Récupère le tuteur lié à l'utilisateur courant en session.
     */
    private function getTuteur(): Tuteur
    {
        return Tuteur::where('id_utilisateur', session('user_id'))->firstOrFail();
    }

    /**
     * Tableau de bord : liste des étudiants encadrés et compteurs
     * (notamment des conventions en attente de validation tuteur).
     */
    public function dashboard()
    {
        $tuteur = $this->getTuteur();

        $etudiants = Suivi::with(['candidature.etudiant.utilisateur','candidature.offre','candidature.convention'])
            ->where('id_tuteur', $tuteur->id)
            ->get();

        $nb_etudiants      = $etudiants->count();

        $nb_conv_attente   = $etudiants->filter(function($s) {
            $conv = $s->candidature->convention ?? null;
            return $conv && $conv->statut_etudiant === 'signe' && $conv->statut_tuteur === 'en_attente';
        })->count();

        return view('tuteur.dashboard', compact('tuteur','etudiants','nb_etudiants','nb_conv_attente'));
    }

    public function commenter(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'contenu'        => 'required|string|max:2000',
        ]);

        $tuteur = $this->getTuteur();
        $suivi  = Suivi::where('id_tuteur', $tuteur->id)
            ->where('id_candidature', $request->id_candidature)
            ->first();

        if (!$suivi) {
            abort(403);
        }

        Commentaire::create([
            'id_candidature' => $request->id_candidature,
            'id_utilisateur' => session('user_id'),
            'contenu'        => $request->contenu,
        ]);

        return redirect()->route('tuteur.dashboard')->with('succes', 'Commentaire ajouté.');
    }

    /**
     * Attribution d'une note (sur 20) à un étudiant encadré.
     * Le firstOrFail garantit que le tuteur ne peut noter que ses propres encadrés.
     */
    public function noter(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'note'           => 'required|numeric|min:0|max:20',
        ]);

        $tuteur = $this->getTuteur();
        $suivi  = Suivi::where('id_tuteur', $tuteur->id)
            ->where('id_candidature', $request->id_candidature)
            ->firstOrFail();

        $suivi->update(['note_finale' => $request->note]);

        return redirect()->route('tuteur.dashboard')->with('succes', 'Note enregistrée.');
    }

    /**
     * Liste les offres de stage publiées (consultation seule).
     * Permet au tuteur de prendre connaissance des offres pour orienter ses étudiants.
     */
    public function offres(Request $request)
    {
        $this->getTuteur();

        $q       = $request->input('q', '');
        $duree   = $request->input('duree', '');
        $lieu    = $request->input('lieu', '');

        $query = OffreStage::with('entreprise');
        if ($q)     $query->where(fn($sq) => $sq->where('titre','like',"%$q%")->orWhere('competences','like',"%$q%")->orWhere('description','like',"%$q%"));
        if ($duree) $query->where('duree', $duree);
        if ($lieu)  $query->where('lieu', 'like', "%$lieu%");

        $offres = $query->orderBy('date_publication', 'desc')->get();

        return view('tuteur.offres', compact('offres','q','duree','lieu'));
    }

    public function demandes()
    {
        $this->getTuteur();

        $demandes = DemandeEncadrement::with('candidature.offre.entreprise')
            ->where('id_utilisateur', session('user_id'))
            ->orderBy('date_demande', 'desc')
            ->get();

        return view('tuteur.demandes', compact('demandes'));
    }

    public function voirCahier(int $id_candidature)
    {
        $tuteur = $this->getTuteur();

        $suivi = Suivi::with(['candidature.etudiant.utilisateur','candidature.offre.entreprise'])
            ->where('id_tuteur', $tuteur->id)
            ->where('id_candidature', $id_candidature)
            ->firstOrFail();

        $candidature = $suivi->candidature;
        $entrees = CahierStage::where('id_candidature', $id_candidature)
            ->orderBy('date_jour','desc')
            ->orderBy('created_at','desc')
            ->get();

        return view('tuteur.cahier', compact('tuteur','candidature','entrees'));
    }

    /**
     * Validation de la convention par le tuteur. Ne peut être effectuée
     * que si l'étudiant a préalablement déposé sa convention signée.
     * Le tuteur valide en parallèle de l'entreprise (workflow non-séquentiel).
     */
    public function validerConvention(int $id)
    {
        $tuteur = $this->getTuteur();

        $convention = Convention::with('candidature')->findOrFail($id);

        $suivi = Suivi::where('id_tuteur', $tuteur->id)
            ->where('id_candidature', $convention->id_candidature)
            ->first();

        if (!$suivi) {
            abort(403);
        }

        if ($convention->statut_etudiant !== 'signe') {
            return back()->with('erreur', 'L\'étudiant n\'a pas encore déposé la convention.');
        }

        $convention->update(['statut_tuteur' => 'signe']);

        return redirect()->route('tuteur.dashboard')->with('succes', 'Convention validée. En attente de l\'administration.');
    }

    public function telechargerConvention(int $id)
    {
        $tuteur = $this->getTuteur();

        $convention = Convention::findOrFail($id);

        $suivi = Suivi::where('id_tuteur', $tuteur->id)
            ->where('id_candidature', $convention->id_candidature)
            ->first();

        if (!$suivi || !$convention->chemin_fichier) {
            abort(403);
        }

        return Storage::download($convention->chemin_fichier, $convention->nom_original ?? basename($convention->chemin_fichier));
    }

    /**
     * Soumission d'une demande d'encadrement à l'administrateur.
     * Le tuteur précise l'étudiant qu'il souhaite encadrer ; l'admin
     * tranchera ensuite (acceptation ou refus avec motif).
     */
    public function soumettreDemande(Request $request)
    {
        $this->getTuteur();

        $request->validate([
            'nom_etudiant'    => 'required|string|max:100',
            'prenom_etudiant' => 'required|string|max:100',
            'numero_etudiant' => 'required|string|max:50',
        ]);

        DemandeEncadrement::create([
            'id_utilisateur'  => session('user_id'),
            'nom_etudiant'    => $request->nom_etudiant,
            'prenom_etudiant' => $request->prenom_etudiant,
            'numero_etudiant' => $request->numero_etudiant,
            'statut'          => 'en_attente',
            'date_demande'    => now(),
        ]);

        return redirect()->route('tuteur.demandes')->with('succes', 'Demande envoyée à l\'administrateur.');
    }
}
