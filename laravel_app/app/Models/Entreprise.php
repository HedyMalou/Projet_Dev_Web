<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $table = 'ENTREPRISE';
    public $timestamps = false;

    protected $fillable = ['id_utilisateur', 'nom_entreprise', 'secteur', 'adresse'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }

    public function offres()
    {
        return $this->hasMany(OffreStage::class, 'id_entreprise');
    }

    public function getNomAttribute()
    {
        return $this->nom_entreprise;
    }
}
