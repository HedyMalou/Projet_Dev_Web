<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jury;
use App\Models\Candidature;
use App\Models\Suivi;
use App\Models\Commentaire;

class JuryController extends Controller
{
    // ── Tableau de bord ───────────────────────────────────────────────────────

    public function dashboard()
    {
        $jury = Jury::where('id_utilisateur', session('user_id'))->firstOrFail();

        $candidatures = Candidature::with(['etudiant.utilisateur','offre.entreprise','suivi'])
            ->where('statut', 'validee')
            ->orderBy('date_candidature', 'desc')
            ->get();

        return view('jury.dashboard', compact('jury','candidatures'));
    }

    // ── Noter un stage ────────────────────────────────────────────────────────

    public function noter(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'note'           => 'required|numeric|min:0|max:20',
        ]);

        // Le jury peut noter n'importe quel stage validé
        $candidature = Candidature::where('statut', 'validee')->findOrFail($request->id_candidature);

        $suivi = Suivi::where('id_candidature', $candidature->id)->first();
        if ($suivi) {
            $suivi->update(['note_finale' => $request->note]);
        }

        return redirect()->route('jury.dashboard')->with('succes', 'Note enregistrée.');
    }

    // ── Ajouter un commentaire ────────────────────────────────────────────────

    public function commenter(Request $request)
    {
        $request->validate([
            'id_candidature' => 'required|integer|exists:CANDIDATURE,id',
            'contenu'        => 'required|string|max:2000',
        ]);

        Commentaire::create([
            'id_candidature' => $request->id_candidature,
            'id_utilisateur' => session('user_id'),
            'contenu'        => $request->contenu,
        ]);

        return redirect()->route('jury.dashboard')->with('succes', 'Commentaire ajouté.');
    }
}
