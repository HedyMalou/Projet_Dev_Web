<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'DOCUMENT';

    const UPDATED_AT = null;
    const CREATED_AT = 'date_depot';

    protected $fillable = ['id_candidature', 'type', 'chemin_fichier'];

    // ── Relations ──────────────────────────────────────────────────────────────

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'id_candidature');
    }
}
