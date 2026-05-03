<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CahierStage extends Model
{
    protected $table = 'CAHIER_STAGE';

    const UPDATED_AT = null;

    protected $fillable = ['id_candidature', 'date_jour', 'contenu'];

    protected $casts = [
        'date_jour'  => 'date',
        'created_at' => 'datetime',
    ];

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'id_candidature');
    }
}
