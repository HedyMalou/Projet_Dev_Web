<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suivi extends Model
{
    protected $table = 'SUIVI';
    public $timestamps = false;

    protected $fillable = ['id_tuteur', 'id_candidature', 'note_finale', 'date_debut'];

    protected $casts = [
        'note_finale' => 'float',
        'date_debut'  => 'date',
    ];

    public function tuteur()
    {
        return $this->belongsTo(Tuteur::class, 'id_tuteur');
    }

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'id_candidature');
    }
}
