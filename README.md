# Projet Dev Web — Plateforme de gestion des stages

Projet réalisé dans le cadre du cours de Développement Web ING1 à CY Tech (2025-2026).

## Description

Plateforme web de gestion et d'archivage des stages pour les étudiants, enseignants (tuteurs), jurys et entreprises de CY Tech.

Fonctionnalités principales :
- Authentification par rôle (étudiant, tuteur, jury, entreprise, admin) avec double authentification (A2F)
- Dépôt et suivi de candidatures
- Publication et gestion des offres de stage
- Suivi pédagogique (notes, commentaires, conventions)
- Archivage des dossiers de stage

## Équipe

- **ANCELIN Titouan** — Chef de projet / Back-end (authentification, sessions, logique PHP)
- **OUERGHI Hedy** — Front-end (interfaces HTML/CSS/JS, Bootstrap) / Back-end (scripts PHP, gestion fichiers)
- **GARRA Jeremy** — Base de données (MCD/MLD, MySQL) / Tests et documentation

## Technologies

- **Laravel 11** (PHP 8.2+) — framework back-end
- **Blade** — moteur de templates
- **MySQL / MariaDB** — base de données
- **Bootstrap 5** — composants CSS

---

## Installation et lancement

### Prérequis

| Outil       | Version minimale |
|-------------|-----------------|
| PHP         | 8.2             |
| Composer    | 2.x             |
| MySQL       | 8.0 / MariaDB 10.6 |
| Git         | 2.x             |

---

### Linux (Ubuntu / Debian)

#### 1. Cloner le dépôt

```bash
git clone https://github.com/HedyMalou/Projet_Dev_Web.git
cd Projet_Dev_Web/laravel_app
```

#### 2. Installer les dépendances PHP

```bash
composer install
```

#### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Ouvrir `.env` et renseigner les accès à la base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_la_base
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

#### 4. Créer la base de données et exécuter les migrations

```bash
mysql -u votre_utilisateur -p -e "CREATE DATABASE IF NOT EXISTS nom_de_la_base CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

#### 5. (Optionnel) Insérer des données de test

```bash
php artisan db:seed
```

#### 6. Lancer le serveur de développement

```bash
php artisan serve
```

Accéder à l'application : [http://localhost:8000](http://localhost:8000)

---

### Windows

#### 1. Cloner le dépôt

Dans un terminal (Git Bash, PowerShell ou CMD) :

```bash
git clone https://github.com/HedyMalou/Projet_Dev_Web.git
cd Projet_Dev_Web\laravel_app
```

#### 2. Installer les dépendances PHP

```bash
composer install
```

#### 3. Configurer l'environnement

```bash
copy .env.example .env
php artisan key:generate
```

Ouvrir `.env` avec un éditeur de texte et renseigner les accès MySQL :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_la_base
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe
```

> Si vous utilisez WAMP/XAMPP, l'utilisateur par défaut est `root` et le mot de passe est vide ou `root`.

#### 4. Créer la base de données et exécuter les migrations

Créer la base via phpMyAdmin ou en ligne de commande :

```bash
mysql -u votre_utilisateur -p -e "CREATE DATABASE IF NOT EXISTS nom_de_la_base CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

#### 5. (Optionnel) Insérer des données de test

```bash
php artisan db:seed
```

#### 6. Lancer le serveur de développement

```bash
php artisan serve
```

Accéder à l'application : [http://localhost:8000](http://localhost:8000)

---

## Comptes de test

Les seeders créent des comptes pour chaque rôle. Les mots de passe sont hashés en BCrypt.

Pour générer un hash depuis le terminal :

```bash
php -r "echo password_hash('votre_mot_de_passe', PASSWORD_BCRYPT);"
```

Pour mettre à jour un mot de passe directement en base :

```bash
mysql -u votre_utilisateur -p nom_de_la_base \
  -e "UPDATE UTILISATEUR SET mot_de_passe='<hash>' WHERE email='<email>';"
```

## Code A2F en développement local

L'A2F envoie un code par email. En local, le mail ne part pas. Récupérer le code directement en base :

```bash
mysql -u votre_utilisateur -p nom_de_la_base \
  -e "SELECT code, date_expiration FROM AUTH_CODE ORDER BY id DESC LIMIT 1;"
```

---

## Structure du projet

```
Projet_Dev_Web/
├── laravel_app/          # Application Laravel principale
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/   # Contrôleurs par rôle
│   │   │   └── Middleware/    # Middleware auth.check
│   │   └── Models/            # Modèles Eloquent (12 tables)
│   ├── database/
│   │   └── migrations/        # 12 migrations (tables métier)
│   ├── resources/views/       # Vues Blade par rôle
│   │   ├── auth/              # Login, register, A2F
│   │   ├── etudiant/
│   │   ├── entreprise/
│   │   ├── tuteur/
│   │   ├── jury/
│   │   ├── admin/
│   │   └── layouts/           # Layout principal avec sidebar
│   └── routes/web.php         # Toutes les routes
├── frontend/             # Ancien front-end PHP (référence)
├── backend/              # Ancien back-end PHP (référence)
├── database/             # Script SQL initial (référence)
└── docs/                 # Rapport et maquettes
```

## Workflow Git

```bash
# Avant de travailler — récupérer les dernières modifications
git pull origin main

# Créer une branche pour une nouvelle fonctionnalité
git checkout -b feature/nom-de-la-feature

# Committer et pousser
git add .
git commit -m "feat: description de la modification"
git push origin feature/nom-de-la-feature
```
