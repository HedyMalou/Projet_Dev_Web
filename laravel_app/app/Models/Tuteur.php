<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuteur extends Model
{
    protected $table = 'TUTEUR';
    public $timestamps = false;

    protected $fillable = ['id_utilisateur', 'departement'];

    // ── Relations ──────────────────────────────────────────────────────────────

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }

    public function suivis()
    {
        return $this->hasMany(Suivi::class, 'id_tuteur');
    }
}
