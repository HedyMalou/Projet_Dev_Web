<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $table = 'ETUDIANT';
    public $timestamps = false;

    protected $fillable = ['id_utilisateur', 'filiere', 'promotion', 'numero_etudiant'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'id_etudiant');
    }
}
