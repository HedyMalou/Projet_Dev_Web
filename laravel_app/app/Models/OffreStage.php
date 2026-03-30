<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffreStage extends Model
{
    protected $table = 'OFFRE_STAGE';

    // Seul created_at présent (date_publication) — pas de updated_at
    const UPDATED_AT = null;
    const CREATED_AT = 'date_publication';

    protected $fillable = [
        'id_entreprise', 'titre', 'description', 'competences', 'duree', 'lieu',
    ];

    // ── Relations ──────────────────────────────────────────────────────────────

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entreprise');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'id_offre');
    }
}
