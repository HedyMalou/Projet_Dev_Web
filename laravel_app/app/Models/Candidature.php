<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $table = 'CANDIDATURE';

    const UPDATED_AT = null;
    const CREATED_AT = 'date_candidature';

    protected $fillable = ['id_etudiant', 'id_offre', 'statut'];

    // ── Relations ──────────────────────────────────────────────────────────────

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant');
    }

    public function offre()
    {
        return $this->belongsTo(OffreStage::class, 'id_offre');
    }

    public function convention()
    {
        return $this->hasOne(Convention::class, 'id_candidature');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'id_candidature');
    }

    public function suivi()
    {
        return $this->hasOne(Suivi::class, 'id_candidature');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class, 'id_candidature');
    }
}
