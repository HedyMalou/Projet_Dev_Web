<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthCode extends Model
{
    protected $table = 'AUTH_CODE';

    // Aucun created_at / updated_at dans cette table
    public $timestamps = false;

    protected $fillable = ['id_utilisateur', 'code', 'date_expiration', 'utilise'];

    protected $casts = [
        'date_expiration' => 'datetime',
        'utilise' => 'boolean',
    ];

    // ── Relations ──────────────────────────────────────────────────────────────

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
