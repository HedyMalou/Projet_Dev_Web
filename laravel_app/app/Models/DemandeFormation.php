<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle DemandeFormation : représente une demande d'ajout de filière
 * soumise par un étudiant et soumise à validation de l'administrateur.
 */
class DemandeFormation extends Model
{
    protected $table = 'DEMANDE_FORMATION';

    public $timestamps = false;

    protected $fillable = [
        'id_etudiant',
        'nom_formation',
        'description',
        'statut',
        'reponse_admin',
        'date_demande',
        'date_traitement',
    ];

    protected $casts = [
        'date_demande'    => 'datetime',
        'date_traitement' => 'datetime',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant');
    }
}
