<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Models\Etudiant;
use App\Models\Tuteur;
use App\Models\Jury;
use App\Models\Entreprise;
use App\Models\OffreStage;

class DatabaseSeeder extends Seeder
{
    /**
     * Recrée le jeu de données initial du schema.sql
     * Mots de passe :
     *   - étudiants / tuteur / jury / entreprise : test1234
     *   - admin (admin@cytech.fr) : admin1234
     *
     * valide=1 pour étudiants et admin, valide=0 pour tuteur/jury/entreprise
     */
    public function run(): void
    {

        $hedy = Utilisateur::create([
            'nom' => 'Ouerghi', 'prenom' => 'Hedy',
            'email' => 'hedy.ouerghi@etu.cyu.fr',
            'mot_de_passe' => Hash::make('test1234'),
            'role' => 'etudiant',
            'valide' => 1,
        ]);
        $jeremy = Utilisateur::create([
            'nom' => 'Garra', 'prenom' => 'Jeremy',
            'email' => 'jeremy.garra@etu.cyu.fr',
            'mot_de_passe' => Hash::make('test1234'),
            'role' => 'etudiant',
            'valide' => 1,
        ]);
        $titouan = Utilisateur::create([
            'nom' => 'Ancelin', 'prenom' => 'Titouan',
            'email' => 'titouan.ancelin@etu.cyu.fr',
            'mot_de_passe' => Hash::make('test1234'),
            'role' => 'tuteur',
            'valide' => 1,
        ]);
        $sophie = Utilisateur::create([
            'nom' => 'Martin', 'prenom' => 'Sophie',
            'email' => 'sophie.martin@etu.cyu.fr',
            'mot_de_passe' => Hash::make('test1234'),
            'role' => 'jury',
            'valide' => 1,
        ]);
        $techcorp = Utilisateur::create([
            'nom' => 'TechCorp', 'prenom' => 'Admin',
            'email' => 'contact@techcorp.fr',
            'mot_de_passe' => Hash::make('test1234'),
            'role' => 'entreprise',
            'valide' => 1,
        ]);

        Utilisateur::create([
            'nom' => 'Admin', 'prenom' => 'CYTech',
            'email' => 'admin@cytech.fr',
            'mot_de_passe' => Hash::make('admin1234'),
            'role' => 'admin',
            'valide' => 1,
        ]);

        Etudiant::create(['id_utilisateur' => $hedy->id,    'filiere' => 'Informatique', 'promotion' => 'ING1', 'numero_etudiant' => 'ETU001']);
        Etudiant::create(['id_utilisateur' => $jeremy->id,  'filiere' => 'Informatique', 'promotion' => 'ING1', 'numero_etudiant' => 'ETU002']);
        Tuteur::create(['id_utilisateur' => $titouan->id,   'departement' => 'Informatique']);
        Jury::create(['id_utilisateur' => $sophie->id,      'specialite' => 'Génie Logiciel']);

        $ent = Entreprise::create([
            'id_utilisateur' => $techcorp->id,
            'nom_entreprise' => 'TechCorp',
            'secteur' => 'Informatique',
            'adresse' => '12 rue de la Tech, Paris',
        ]);

        OffreStage::create(['id_entreprise' => $ent->id, 'titre' => 'Développeur Web PHP',    'description' => "Développement d'une plateforme interne", 'competences' => 'PHP, MySQL, Bootstrap', 'duree' => '3 mois', 'lieu' => 'Paris']);
        OffreStage::create(['id_entreprise' => $ent->id, 'titre' => 'Développeur Front-end', 'description' => "Intégration d'interfaces responsive",    'competences' => 'HTML, CSS, JavaScript',  'duree' => '2 mois', 'lieu' => 'Remote']);
    }
}
