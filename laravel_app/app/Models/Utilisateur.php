<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Utilisateur extends Authenticatable
{
    // Garde le nom de table existant (MAJUSCULES = compatible avec la DB actuelle)
    protected $table = 'UTILISATEUR';

    // Laravel gère created_at mais pas updated_at (absent du schéma)
    const UPDATED_AT = null;

    protected $fillable = [
        'nom', 'prenom', 'email', 'mot_de_passe', 'role', 'valide',
    ];

    // On utilise mot_de_passe au lieu du champ "password" par défaut de Laravel
    protected $hidden = ['mot_de_passe'];

    // Dis à Laravel quel champ contient le mot de passe (pour Auth)
    public function getAuthPassword(): string
    {
        return $this->mot_de_passe;
    }

    // ── Relations ──────────────────────────────────────────────────────────────

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
