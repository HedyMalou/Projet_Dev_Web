# Projet Dev Web — Plateforme de gestion des stages

Projet réalisé dans le cadre du cours de Développement Web ING1 à CY Tech (2025-2026).

## Description

Plateforme web de gestion et d'archivage des stages pour les étudiants, enseignants (tuteurs), jurys et entreprises de CY Tech.

Fonctionnalités principales :
- Authentification par rôle (étudiant, tuteur, jury, entreprise, admin) avec double authentification (A2F)
- Validation des comptes tuteur/jury/entreprise par l'administrateur
- Dépôt et suivi de candidatures
- Publication et gestion des offres de stage
- Affectation des tuteurs aux étudiants par l'admin
- Suivi pédagogique (notes, commentaires, conventions)
- Archivage des dossiers de stage

## Équipe

- **ANCELIN Titouan** — Chef de projet / Back-end (authentification, sessions, logique PHP)
- **OUERGHI Hedy** — Front-end (interfaces HTML/CSS/JS) / Back-end (Laravel, gestion fichiers)
- **GARRA Jeremy** — Base de données (MCD/MLD, MySQL) / Tests et documentation

## Technologies

- **Laravel 11** (PHP 8.2+) — framework back-end
- **Blade** — moteur de templates
- **MySQL / MariaDB** — base de données

---

## Installation et lancement

### Prérequis

| Outil       | Version minimale       |
|-------------|------------------------|
| PHP         | 8.2                    |
| Composer    | 2.x                    |
| MySQL       | 8.0 / MariaDB 10.6     |
| Git         | 2.x                    |

---

### Linux (Ubuntu / Debian)

```bash
git clone https://github.com/HedyMalou/Projet_Dev_Web.git
cd Projet_Dev_Web/laravel_app
composer install
```

Créer la base de données :

```bash
mysql -u <utilisateur> -p -e "CREATE DATABASE IF NOT EXISTS stages_cytech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Adapter les credentials dans `.env` :

```env
DB_USERNAME=<utilisateur>
DB_PASSWORD=<mot_de_passe>
```

Lancer les migrations et le seed :

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Accéder à l'application : [http://localhost:8000](http://localhost:8000)

---

### Windows

Dans Git Bash, PowerShell ou CMD :

```bash
git clone https://github.com/HedyMalou/Projet_Dev_Web.git
cd Projet_Dev_Web\laravel_app
composer install
```

Créer la base via phpMyAdmin ou en ligne de commande :

```bash
mysql -u <utilisateur> -p -e "CREATE DATABASE IF NOT EXISTS stages_cytech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Adapter les credentials dans `.env` :

```env
DB_USERNAME=<utilisateur>
DB_PASSWORD=<mot_de_passe>
```

> Sous WAMP/XAMPP, l'utilisateur par défaut est `root` avec un mot de passe vide.

Lancer les migrations et le seed :

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Accéder à l'application : [http://localhost:8000](http://localhost:8000)

---

## Comptes disponibles (après seed)

| Rôle        | Email                          | Mot de passe |
|-------------|--------------------------------|--------------|
| Admin       | admin@cytech.fr                | admin1234    |
| Étudiant    | hedy.ouerghi@etu.cyu.fr        | test1234     |
| Tuteur      | titouan.ancelin@etu.cyu.fr     | test1234     |
| Jury        | sophie.martin@etu.cyu.fr       | test1234     |
| Entreprise  | contact@techcorp.fr            | test1234     |

> Les étudiants peuvent se connecter directement après inscription.
> Les tuteurs, jurys et entreprises doivent être validés par l'admin.

## Code A2F en développement local

L'A2F est activée. En local, le code ne part pas par email — le récupérer directement en base :

```bash
mysql -u <utilisateur> -p stages_cytech \
  -e "SELECT code, date_expiration FROM AUTH_CODE ORDER BY id DESC LIMIT 1;"
```

---

## Structure du projet

```
Projet_Dev_Web/
├── laravel_app/               # Application Laravel principale
│   ├── app/
│   │   ├── Http/Controllers/  # Contrôleurs par rôle
│   │   └── Models/            # Modèles Eloquent
│   ├── database/
│   │   ├── migrations/        # Migrations (tables métier)
│   │   └── seeders/           # Données de test
│   ├── resources/views/       # Vues Blade par rôle
│   │   ├── auth/
│   │   ├── admin/
│   │   ├── etudiant/
│   │   ├── entreprise/
│   │   ├── tuteur/
│   │   ├── jury/
│   │   └── layouts/
│   └── routes/web.php
├── frontend/                  # Ancien front-end PHP (référence)
├── backend/                   # Ancien back-end PHP (référence)
├── database/                  # Script SQL initial (référence)
└── docs/                      # Rapport et maquettes
```
