<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    protected $table = 'MISSION';

    const UPDATED_AT = null;

    protected $fillable = ['id_candidature', 'titre', 'description', 'statut'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'id_candidature');
    }
}
