<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    protected $table = 'COMMENTAIRE';

    const UPDATED_AT = null;
    const CREATED_AT = 'date';

    protected $fillable = ['id_candidature', 'id_utilisateur', 'contenu'];

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'id_candidature');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
