<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthCode extends Model
{
    protected $table = 'AUTH_CODE';

    public $timestamps = false;

    protected $fillable = ['id_utilisateur', 'code', 'date_expiration', 'utilise'];

    protected $casts = [
        'date_expiration' => 'datetime',
        'utilise' => 'boolean',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
