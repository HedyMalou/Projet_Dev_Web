<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{
    protected $table = 'JURY';
    public $timestamps = false;

    protected $fillable = ['id_utilisateur', 'specialite'];

    // ── Relations ──────────────────────────────────────────────────────────────

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
