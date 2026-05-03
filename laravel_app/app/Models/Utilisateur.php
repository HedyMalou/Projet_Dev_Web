<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Utilisateur extends Authenticatable
{

    protected $table = 'UTILISATEUR';

    const UPDATED_AT = null;

    protected $fillable = [
        'nom', 'prenom', 'email', 'mot_de_passe', 'role', 'valide',
    ];

    protected $hidden = ['mot_de_passe'];

    public function getAuthPassword(): string
    {
        return $this->mot_de_passe;
    }

    public function authCodes()
    {
        return $this->hasMany(AuthCode::class, 'id_utilisateur');
    }

    public function etudiant()
    {
        return $this->hasOne(Etudiant::class, 'id_utilisateur');
    }

    public function tuteur()
    {
        return $this->hasOne(Tuteur::class, 'id_utilisateur');
    }

    public function jury()
    {
        return $this->hasOne(Jury::class, 'id_utilisateur');
    }

    public function entreprise()
    {
        return $this->hasOne(Entreprise::class, 'id_utilisateur');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class, 'id_utilisateur');
    }
}
