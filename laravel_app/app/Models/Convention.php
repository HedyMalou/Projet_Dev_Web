<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convention extends Model
{
    protected $table = 'CONVENTION';
    public $timestamps = false;

    protected $fillable = [
        'id_candidature',
        'chemin_fichier',
        'nom_original',
        'statut_etudiant',
        'statut_entreprise',
        'statut_tuteur',
        'statut_admin',
    ];

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'id_candidature');
    }
}
