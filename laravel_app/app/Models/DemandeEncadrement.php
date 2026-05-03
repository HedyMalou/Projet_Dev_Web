<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeEncadrement extends Model
{
    protected $table = 'DEMANDE_ENCADREMENT';
    public $timestamps = false;

    protected $fillable = [
        'id_utilisateur',
        'nom_etudiant',
        'prenom_etudiant',
        'numero_etudiant',
        'id_candidature',
        'statut',
        'motif_refus',
        'date_demande',
        'date_traitement',
    ];

    protected $casts = [
        'date_demande'    => 'datetime',
        'date_traitement' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'id_candidature');
    }
}
