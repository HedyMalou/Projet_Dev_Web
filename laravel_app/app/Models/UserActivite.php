<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivite extends Model
{
    protected $table = 'USER_ACTIVITE';
    public $timestamps = false;

    protected $fillable = ['id_utilisateur', 'type', 'detail', 'date_action'];

    protected $casts = [
        'date_action' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
