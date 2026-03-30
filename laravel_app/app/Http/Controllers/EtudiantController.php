<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Etudiant;
use App\Models\Candidature;
use App\Models\Document;
use App\Models\Commentaire;
use App\Models\OffreStage;
use App\Models\Utilisateur;

class EtudiantController extends Controller
{
    private function getEtudiant(): Etudiant
    {
        return Etudiant::where('id_utilisateur', session('user_id'))->firstOrFail();
    }

    // ── Tableau de bord ───────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $etudiant = $this->getEtudiant();

        $nb_candidatures = Candidature::where('id_etudiant', $etudiant->id)->count();
        $nb_documents    = Document::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))->count();

        // Filtres de recherche
        $q     = $request->input('q', '');
        $duree = $request->input('duree', '');
        $lieu  = $request->input('lieu', '');

        $query = OffreStage::with('entreprise');
        if ($q)    $query->where(fn($sq) => $sq->where('titre','like',"%$q%")->orWhere('competences','like',"%$q%")->orWhere('description','like',"%$q%"));
        if ($duree) $query->where('duree', $duree);
        if ($lieu)  $query->where('lieu', 'like', "%$lieu%");

        $offres = $query->orderBy('date_publication', 'desc')->limit(6)->get();

        $offres_postulees = Candidature::where('id_etudiant', $etudiant->id)->pluck('id_offre')->toArray();

        return view('etudiant.dashboard', compact('etudiant','nb_candidatures','nb_documents','offres','offres_postulees','q','duree','lieu'));
    }

    // ── Mon dossier ───────────────────────────────────────────────────────────

    public function monDossier()
    {
        $etudiant = $this->getEtudiant();

        $candidatures = Candidature::with(['offre.entreprise','convention'])
            ->where('id_etudiant', $etudiant->id)
            ->orderBy('date_candidature', 'desc')
            ->get();

        // Documents indexés par candidature
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

    // ── Documents ─────────────────────────────────────────────────────────────

    public function documents()
    {
        $etudiant = $this->getEtudiant();

        $documents = Document::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))
            ->with(['candidature.offre.entreprise'])
            ->orderBy('date_depot','desc')
            ->get();

        $candidatures_valides = Candidature::with('offre.entreprise')
            ->where('id_etudiant', $etudiant->id)
            ->whereIn('statut',['validee','archivee'])
            ->get();

        return view('etudiant.documents', compact('etudiant','documents','candidatures_valides'));
    }

    // ── Profil ────────────────────────────────────────────────────────────────

    public function profil()
    {
        $etudiant = Etudiant::with('utilisateur')
            ->where('id_utilisateur', session('user_id'))
            ->firstOrFail();

        $nb_cand = Candidature::where('id_etudiant', $etudiant->id)->count();
        $nb_doc  = Document::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))->count();

        return view('etudiant.profil', compact('etudiant','nb_cand','nb_doc'));
    }

    // ── Postuler ──────────────────────────────────────────────────────────────

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

        foreach (['cv', 'lettre_motivation'] as $type) {
            $chemin = $request->file($type)->store('uploads');
            Document::create([
                'id_candidature' => $candidature->id,
                'type'           => $type,
                'chemin_fichier' => $chemin,
            ]);
        }

        return redirect()->route('etudiant.dashboard')->with('succes', 'Candidature envoyée avec vos documents !');
    }

    public function telechargerDocument(int $id)
    {
        $etudiant = $this->getEtudiant();

        $document = Document::whereHas('candidature', fn($q) => $q->where('id_etudiant', $etudiant->id))
            ->findOrFail($id);

        return Storage::download($document->chemin_fichier);
    }
}
