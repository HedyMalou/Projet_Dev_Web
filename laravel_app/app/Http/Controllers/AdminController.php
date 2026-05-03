<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Utilisateur;
use App\Models\Etudiant;
use App\Models\Tuteur;
use App\Models\Jury;
use App\Models\OffreStage;
use App\Models\Candidature;
use App\Models\Suivi;
use App\Models\Convention;
use App\Models\DemandeEncadrement;
use App\Models\AffectationJury;
use App\Models\UserActivite;
use App\Models\DemandeFormation;

/**
 * Contrôleur d'administration de la plateforme.
 *
 * Centralise les opérations à privilèges élevés : validation de comptes,
 * modification de rôles, modération des offres, archivage et désarchivage
 * des dossiers, affectation des tuteurs et jurys, validation finale des
 * conventions de stage et traitement des demandes d'ajout de formation.
 */
class AdminController extends Controller
{
    /**
     * Tableau de bord administrateur : KPI globaux et activité récente.
     */
    public function dashboard()
    {
        $admin = Utilisateur::findOrFail(session('user_id'));

        $nb_etudiants = Etudiant::count();
        $nb_offres    = OffreStage::count();
        $nb_stages    = Candidature::where('statut','validee')->count();
        $nb_users     = Utilisateur::count();

        $activites = UserActivite::selectRaw('id_utilisateur, type, COUNT(*) as nb, MAX(date_action) as dernier')
            ->groupBy('id_utilisateur','type')
            ->get()
            ->groupBy('id_utilisateur');

        $users = Utilisateur::where('valide', 1)->orderBy('created_at','desc')->get();

        $comptes_en_attente = Utilisateur::where('valide', 0)->orderBy('created_at','desc')->get();

        $candidatures_validees = Candidature::with(['etudiant.utilisateur','offre'])
            ->where('statut','validee')
            ->get();

        $tuteurs = Tuteur::with('utilisateur')->get();

        return view('admin.dashboard', compact(
            'admin','nb_etudiants','nb_offres','nb_stages','nb_users',
            'users','comptes_en_attente','candidatures_validees','tuteurs','activites'
        ));
    }

    /**
     * Active un compte tuteur, jury ou entreprise après vérification.
     * Sans cette validation, le compte ne peut pas se connecter.
     */
    public function validerCompte(int $id)
    {
        Utilisateur::findOrFail($id)->update(['valide' => 1]);

        return redirect()->route('admin.dashboard')->with('succes', 'Compte validé.');
    }

    public function refuserCompte(int $id)
    {
        Utilisateur::findOrFail($id)->delete();

        return redirect()->route('admin.dashboard')->with('succes', 'Compte refusé et supprimé.');
    }

    /**
     * Modifie le rôle d'un utilisateur (étudiant, tuteur, jury, entreprise, admin).
     * Crée ou supprime les profils spécifiques associés en fonction du nouveau rôle.
     */
    public function modifierRole(Request $request, int $id)
    {
        $request->validate([
            'role' => 'required|in:etudiant,tuteur,jury,entreprise,admin',
        ]);

        $user = Utilisateur::findOrFail($id);

        if ($user->id === (int)session('user_id')) {
            return back()->with('erreur', 'Vous ne pouvez pas modifier votre propre rôle.');
        }

        if ($user->role === $request->role) {
            return back()->with('erreur', 'L\'utilisateur a déjà ce rôle.');
        }

        match ($user->role) {
            'etudiant'   => Etudiant::where('id_utilisateur', $user->id)->delete(),
            'tuteur'     => Tuteur::where('id_utilisateur', $user->id)->delete(),
            'jury'       => Jury::where('id_utilisateur', $user->id)->delete(),
            'entreprise' => \App\Models\Entreprise::where('id_utilisateur', $user->id)->delete(),
            default      => null,
        };

        match ($request->role) {
            'etudiant'   => Etudiant::create([
                'id_utilisateur'  => $user->id,
                'filiere'         => 'Informatique',
                'promotion'       => 'ING1',
                'numero_etudiant' => 'ETU'.$user->id.'-'.time(),
            ]),
            'tuteur'     => Tuteur::create(['id_utilisateur' => $user->id, 'departement' => '—']),
            'jury'       => Jury::create(['id_utilisateur' => $user->id, 'specialite' => '—']),
            'entreprise' => \App\Models\Entreprise::create(['id_utilisateur' => $user->id, 'nom_entreprise' => '—']),
            default      => null,
        };

        $user->update(['role' => $request->role]);

        return redirect()->route('admin.dashboard')->with('succes', 'Rôle modifié.');
    }

    public function supprimerUser(Request $request)
    {
        $request->validate(['supprimer_id' => 'required|integer']);

        $id = (int)$request->supprimer_id;

        if ($id === (int)session('user_id')) {
            return redirect()->route('admin.dashboard')->with('erreur', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        Utilisateur::findOrFail($id)->delete();

        return redirect()->route('admin.dashboard')->with('succes', 'Utilisateur supprimé.');
    }

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

    /**
     * Archive une candidature validée. Les données sont conservées
     * pour pouvoir être réutilisées d'une année à l'autre.
     */
    public function archiver(Request $request)
    {
        $request->validate(['id_candidature' => 'required|integer']);

        $candidature = Candidature::where('statut','validee')->findOrFail($request->id_candidature);
        $candidature->update(['statut' => 'archivee']);

        return redirect()->route('admin.archivage')->with('succes', 'Dossier archivé.');
    }

    /**
     * Réactive une candidature archivée pour réutiliser les informations
     * de l'étudiant (nom, filière, etc.) sur un nouveau cycle.
     */
    public function desarchiver(int $id)
    {
        $candidature = Candidature::where('statut','archivee')->findOrFail($id);
        $candidature->update(['statut' => 'validee']);

        return redirect()->route('admin.archivage')->with('succes', 'Dossier réactivé. Les informations de l\'étudiant sont à nouveau disponibles.');
    }

    public function encadrement()
    {
        $admin = Utilisateur::findOrFail(session('user_id'));

        $demandes_en_attente = DemandeEncadrement::with('utilisateur')
            ->where('statut', 'en_attente')
            ->orderBy('date_demande', 'desc')
            ->get();

        $demandes_traitees = DemandeEncadrement::with(['utilisateur','candidature.offre.entreprise'])
            ->whereIn('statut', ['acceptee','refusee'])
            ->orderBy('date_traitement', 'desc')
            ->limit(20)
            ->get();

        $candidatures_validees = Candidature::with(['etudiant.utilisateur','offre.entreprise'])
            ->where('statut', 'validee')
            ->get();

        $jurys = Jury::with('utilisateur')->get();

        $affectations_jury = AffectationJury::with([
            'jury.utilisateur',
            'candidature.etudiant.utilisateur',
            'candidature.offre.entreprise',
        ])->orderBy('date_affectation','desc')->get();

        $conventions_a_valider = Convention::with([
            'candidature.etudiant.utilisateur',
            'candidature.offre.entreprise',
            'candidature.suivi.tuteur.utilisateur',
        ])
            ->where('statut_tuteur', 'signe')
            ->where('statut_entreprise', 'signe')
            ->where('statut_admin', 'en_attente')
            ->whereNotNull('chemin_fichier')
            ->get();

        return view('admin.encadrement', compact(
            'admin','demandes_en_attente','demandes_traitees',
            'candidatures_validees','jurys','affectations_jury',
            'conventions_a_valider'
        ));
    }

    public function accepterDemande(Request $request, int $id)
    {
        $request->validate(['id_candidature' => 'required|integer|exists:CANDIDATURE,id']);

        $demande = DemandeEncadrement::where('statut','en_attente')->findOrFail($id);

        $candidature = Candidature::with('etudiant.utilisateur')
            ->where('statut','validee')
            ->findOrFail($request->id_candidature);

        $etu = $candidature->etudiant;
        if (!$etu
            || strcasecmp($etu->numero_etudiant, $demande->numero_etudiant) !== 0
            || strcasecmp($etu->utilisateur->nom ?? '', $demande->nom_etudiant) !== 0
            || strcasecmp($etu->utilisateur->prenom ?? '', $demande->prenom_etudiant) !== 0) {
            return back()->with('erreur', 'La candidature sélectionnée ne correspond pas aux informations de la demande.');
        }

        $tuteur = Tuteur::where('id_utilisateur', $demande->id_utilisateur)->firstOrFail();

        Suivi::updateOrCreate(
            ['id_candidature' => $candidature->id],
            ['id_tuteur'      => $tuteur->id]
        );

        $demande->update([
            'statut'          => 'acceptee',
            'id_candidature'  => $candidature->id,
            'date_traitement' => now(),
        ]);

        return redirect()->route('admin.encadrement')->with('succes', 'Demande acceptée, tuteur affecté.');
    }

    public function refuserDemande(Request $request, int $id)
    {
        $request->validate([
            'motif_refus' => 'required|string|max:500',
        ], [
            'motif_refus.required' => 'Le motif de refus est obligatoire.',
        ]);

        $demande = DemandeEncadrement::where('statut','en_attente')->findOrFail($id);

        $demande->update([
            'statut'          => 'refusee',
            'motif_refus'     => $request->motif_refus,
            'date_traitement' => now(),
        ]);

        return redirect()->route('admin.encadrement')->with('succes', 'Demande refusée.');
    }

    /**
     * Affecte un membre de jury à une candidature validée.
     * Plusieurs jurys peuvent être affectés à une même candidature.
     */
    public function affecterJury(Request $request)
    {
        $request->validate([
            'id_jury'        => 'required|integer|exists:JURY,id',
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
        ]);

        $candidature = Candidature::where('statut','validee')->findOrFail($request->id_candidature);

        $existe = AffectationJury::where('id_jury', $request->id_jury)
            ->where('id_candidature', $candidature->id)
            ->exists();

        if ($existe) {
            return redirect()->route('admin.encadrement')->with('erreur', 'Ce jury est déjà affecté à cette soutenance.');
        }

        AffectationJury::create([
            'id_jury'         => $request->id_jury,
            'id_candidature'  => $candidature->id,
            'date_affectation'=> now(),
        ]);

        return redirect()->route('admin.encadrement')->with('succes', 'Jury affecté à la soutenance.');
    }

    public function retirerJury(int $id)
    {
        AffectationJury::findOrFail($id)->delete();
        return redirect()->route('admin.encadrement')->with('succes', 'Affectation retirée.');
    }

    /**
     * Validation finale de la convention par l'administration.
     * Ne peut être effectuée que si le tuteur ET l'entreprise ont déjà signé,
     * verrouillant ainsi le workflow de validation à 3 étages.
     */
    public function validerConvention(int $id)
    {
        $convention = Convention::findOrFail($id);

        if ($convention->statut_tuteur !== 'signe' || $convention->statut_entreprise !== 'signe') {
            return back()->with('erreur', 'Le tuteur pédagogique ET l\'entreprise doivent valider avant l\'administration.');
        }

        $convention->update(['statut_admin' => 'signe']);

        return redirect()->route('admin.encadrement')->with('succes', 'Convention validée par l\'administration.');
    }

    public function telechargerConvention(int $id)
    {
        $convention = Convention::findOrFail($id);

        if (!$convention->chemin_fichier) {
            abort(404);
        }

        return Storage::download($convention->chemin_fichier, $convention->nom_original ?? basename($convention->chemin_fichier));
    }

    /**
     * Affiche la liste des demandes d'ajout de formation soumises par les étudiants.
     * Trie les demandes par statut (en attente d'abord) puis par date.
     */
    public function demandesFormation()
    {
        $demandes = DemandeFormation::with('etudiant.utilisateur')
            ->orderByRaw("FIELD(statut, 'en_attente', 'validee', 'refusee')")
            ->orderBy('date_demande', 'desc')
            ->get();

        return view('admin.demandes_formation', compact('demandes'));
    }

    /**
     * Valide une demande de formation : la formation est désormais utilisable
     * par les étudiants pour leur profil.
     */
    public function validerDemandeFormation(Request $request, int $id)
    {
        $request->validate([
            'reponse' => 'nullable|string|max:500',
        ]);

        $demande = DemandeFormation::findOrFail($id);
        $demande->update([
            'statut'          => 'validee',
            'reponse_admin'   => $request->reponse,
            'date_traitement' => now(),
        ]);

        return redirect()->route('admin.demandes-formation')
            ->with('succes', 'Demande validée. La formation est désormais disponible.');
    }

    /**
     * Refuse une demande de formation avec un motif obligatoire.
     */
    public function refuserDemandeFormation(Request $request, int $id)
    {
        $request->validate([
            'reponse' => 'required|string|max:500',
        ]);

        $demande = DemandeFormation::findOrFail($id);
        $demande->update([
            'statut'          => 'refusee',
            'reponse_admin'   => $request->reponse,
            'date_traitement' => now(),
        ]);

        return redirect()->route('admin.demandes-formation')
            ->with('succes', 'Demande refusée.');
    }
}
