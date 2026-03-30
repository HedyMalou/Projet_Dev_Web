<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convention extends Model
{
    protected $table = 'CONVENTION';
    public $timestamps = false;

    protected $fillable = [
        'id_candidature', 'statut_etudiant', 'statut_entreprise', 'statut_tuteur',
    ];

    // ── Relations ──────────────────────────────────────────────────────────────

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'id_candidature');
    }
}
