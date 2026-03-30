<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tuteur;
use App\Models\Suivi;
use App\Models\Commentaire;
use App\Models\Candidature;

class TuteurController extends Controller
{
    private function getTuteur(): Tuteur
    {
        return Tuteur::where('id_utilisateur', session('user_id'))->firstOrFail();
    }

    // ── Tableau de bord ───────────────────────────────────────────────────────

    public function dashboard()
    {
        $tuteur = $this->getTuteur();

        $etudiants = Suivi::with(['candidature.etudiant.utilisateur','candidature.offre'])
            ->where('id_tuteur', $tuteur->id)
            ->get();

        $nb_etudiants      = $etudiants->count();
        $nb_conv_attente   = $etudiants->filter(fn($s) => $s->candidature->statut === 'en_attente')->count();

        return view('tuteur.dashboard', compact('tuteur','etudiants','nb_etudiants','nb_conv_attente'));
    }

    // ── Ajouter un commentaire ────────────────────────────────────────────────

    public function commenter(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'contenu'        => 'required|string|max:2000',
        ]);

        // Vérifier que le tuteur est bien affecté à cette candidature
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

    // ── Noter un stage ────────────────────────────────────────────────────────

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
}
