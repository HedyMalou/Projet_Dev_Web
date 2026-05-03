<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jury;
use App\Models\Candidature;
use App\Models\Suivi;
use App\Models\Commentaire;
use App\Models\AffectationJury;
use App\Models\CahierStage;

/**
 * Contrôleur de l'espace jury.
 *
 * Le jury évalue les stages des étudiants qui lui sont affectés par
 * l'administrateur. Il peut consulter le cahier de stage, attribuer une
 * note finale et laisser des commentaires sur les dossiers.
 */
class JuryController extends Controller
{
    /**
     * Récupère le jury lié à l'utilisateur courant en session.
     */
    private function getJury(): Jury
    {
        return Jury::where('id_utilisateur', session('user_id'))->firstOrFail();
    }

    /**
     * Tableau de bord : liste des candidatures affectées au jury par l'admin,
     * avec le suivi en cours (note actuelle, étudiant, offre).
     */
    public function dashboard()
    {
        $jury = $this->getJury();

        $candidatures = Candidature::with(['etudiant.utilisateur','offre.entreprise','suivi'])
            ->whereIn('id', AffectationJury::where('id_jury', $jury->id)->pluck('id_candidature'))
            ->orderBy('date_candidature', 'desc')
            ->get();

        return view('jury.dashboard', compact('jury','candidatures'));
    }

    /**
     * Attribution de la note finale (sur 20) par le jury.
     * Met à jour la colonne note_finale du suivi correspondant.
     */
    public function noter(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'note'           => 'required|numeric|min:0|max:20',
        ]);

        $jury = $this->getJury();
        $this->assurerAffectation($jury->id, $request->id_candidature);

        $suivi = Suivi::where('id_candidature', $request->id_candidature)->first();
        if ($suivi) {
            $suivi->update(['note_finale' => $request->note]);
        }

        return redirect()->route('jury.dashboard')->with('succes', 'Note enregistrée.');
    }

    public function commenter(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'contenu'        => 'required|string|max:2000',
        ]);

        $jury = $this->getJury();
        $this->assurerAffectation($jury->id, $request->id_candidature);

        Commentaire::create([
            'id_candidature' => $request->id_candidature,
            'id_utilisateur' => session('user_id'),
            'contenu'        => $request->contenu,
        ]);

        return redirect()->route('jury.dashboard')->with('succes', 'Commentaire ajouté.');
    }

    /**
     * Affiche le cahier de stage d'une candidature affectée au jury.
     * Lecture seule : le jury peut suivre l'avancement quotidien rapporté par l'étudiant.
     */
    public function cahier(int $id_candidature)
    {
        $jury = $this->getJury();
        $this->assurerAffectation($jury->id, $id_candidature);

        $candidature = Candidature::with(['etudiant.utilisateur','offre.entreprise'])
            ->findOrFail($id_candidature);

        $entrees = CahierStage::where('id_candidature', $id_candidature)
            ->orderBy('date_jour', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('jury.cahier', compact('jury','candidature','entrees'));
    }

    /**
     * Vérifie qu'un jury est bien affecté à une candidature donnée.
     * Lève un 403 sinon : empêche un jury de noter ou commenter une
     * candidature qui ne lui a pas été assignée par l'administrateur.
     */
    private function assurerAffectation(int $idJury, int $idCandidature): void
    {
        $affecte = AffectationJury::where('id_jury', $idJury)
            ->where('id_candidature', $idCandidature)
            ->exists();

        if (!$affecte) {
            abort(403);
        }
    }
}
