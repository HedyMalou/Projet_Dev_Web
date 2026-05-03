# CY Tech Stages — Plateforme de gestion des stages

Projet réalisé dans le cadre du module **Projet Développement Web** ING1 à CY Tech (2025–2026).

Plateforme web de gestion et d'archivage des stages pour les étudiants, tuteurs pédagogiques, jurys, entreprises et l'administration de CY Tech.

---

## Sommaire

1. [Équipe](#équipe)
2. [Fonctionnalités](#fonctionnalités)
3. [Pile technique](#pile-technique)
4. [Installation pas à pas — Linux (Ubuntu / Debian)](#installation-pas-à-pas--linux-ubuntu--debian)
5. [Installation pas à pas — Windows](#installation-pas-à-pas--windows)
6. [Installation pas à pas — macOS](#installation-pas-à-pas--macos)
7. [Configuration de la base de données](#configuration-de-la-base-de-données)
8. [Lancement de l'application](#lancement-de-lapplication)
9. [Comptes de test](#comptes-de-test)
10. [Récupération du code de double authentification (2FA) en local](#récupération-du-code-de-double-authentification-2fa-en-local)
11. [Structure du projet](#structure-du-projet)
12. [Dépannage](#dépannage)

---

## Équipe

| Membre               | Rôle                                                       |
|----------------------|------------------------------------------------------------|
| **ANCELIN Titouan**  | Chef de projet — Backend (logique métier, sessions, auth)  |
| **OUERGHI Hedy**     | Frontend — Intégration (vues, design, formulaires)         |
| **GARRA Jeremy**     | Base de données — Tests — Documentation                    |

---

## Fonctionnalités

- Authentification par rôle (étudiant, tuteur, jury, entreprise, admin) avec **double authentification (2FA)** par email
- Validation des comptes tuteur / jury / entreprise par l'administrateur
- Recherche d'offres de stage avec filtres (durée, lieu, filière, mots-clés)
- Dépôt et suivi de candidatures (CV + lettre de motivation)
- Workflow de convention multi-validateurs (étudiant → entreprise + tuteur → admin)
- Cahier de stage et missions attribuées par l'entreprise
- Commentaires bidirectionnels entre tous les acteurs
- Notation par tuteur et jury (avec consultation du cahier de stage côté jury)
- Demande d'ajout d'une nouvelle filière par l'étudiant, validée par l'admin
- Tableau de bord administrateur : validation comptes, archivage, affectations, modération
- Profil public consultable
- Journalisation automatique des accès et actions

---

## Pile technique

| Composant       | Version recommandée               |
|-----------------|-----------------------------------|
| **PHP**         | 8.2 ou 8.3                        |
| **Composer**    | 2.x                               |
| **MySQL**       | 8.0 (ou MariaDB 10.6+)            |
| **Git**         | 2.x                               |
| **Framework**   | Laravel 11                        |
| **Frontend**    | Bootstrap 5 + CSS personnalisé    |

---

## Installation pas à pas — Linux (Ubuntu / Debian)

> Ce guide part d'un système **vierge**. Si vous avez déjà PHP, Composer et MySQL installés, sautez les sections concernées.

### 1.  Mettre à jour le système

```bash
sudo apt update && sudo apt upgrade -y
```

### 2.  Installer PHP 8.2+ et les extensions nécessaires

```bash
sudo apt install -y php php-cli php-common php-mysql php-zip php-gd \
                    php-mbstring php-curl php-xml php-bcmath php-tokenizer \
                    php-fileinfo unzip
```

Vérifier l'installation :
```bash
php --version
```
Vous devez voir au minimum `PHP 8.2`.

> **Si votre Ubuntu fournit une version plus ancienne** (par exemple 7.4) :
> ```bash
> sudo apt install -y software-properties-common
> sudo add-apt-repository ppa:ondrej/php -y
> sudo apt update
> sudo apt install -y php8.3 php8.3-cli php8.3-mysql php8.3-zip php8.3-gd \
>                     php8.3-mbstring php8.3-curl php8.3-xml php8.3-bcmath
> ```

### 3.  Installer Composer

```bash
sudo apt install -y composer
```

ou (méthode officielle, version la plus récente) :
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

Vérifier :
```bash
composer --version
```

### 4.  Installer MySQL

```bash
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql
```

Sécuriser l'installation (facultatif mais recommandé) :
```bash
sudo mysql_secure_installation
```

### 5.  Installer Git

```bash
sudo apt install -y git
```

### 6.  Cloner le projet

```bash
git clone https://github.com/HedyMalou/Projet_Dev_Web.git
cd Projet_Dev_Web/laravel_app
```

### 7.  Installer les dépendances PHP

```bash
composer install
```

Si Composer demande de copier `.env.example` vers `.env`, le fichier `.env` est déjà fourni avec une configuration locale fonctionnelle.

---

## Installation pas à pas — Windows

### 1.  Installer XAMPP (recommandé pour Windows)

XAMPP fournit en une fois PHP, MySQL et phpMyAdmin.

- Télécharger : <https://www.apachefriends.org/fr/index.html>
- Choisir une version contenant **PHP 8.2 ou supérieur**
- Installer dans `C:\xampp` (chemin par défaut)
- Lancer **XAMPP Control Panel** → démarrer **Apache** et **MySQL**

### 2.  Ajouter PHP au PATH Windows

Pour pouvoir utiliser `php` et `composer` depuis n'importe quel terminal :

1. Menu Démarrer → rechercher « variables d'environnement »
2. *Variables d'environnement* → sélectionner `Path` → *Modifier* → *Nouveau*
3. Ajouter : `C:\xampp\php`
4. Valider, **redémarrer le terminal**

Vérifier dans **PowerShell** ou **CMD** :
```bat
php --version
```

### 3.  Installer Composer

- Télécharger l'installeur Windows : <https://getcomposer.org/Composer-Setup.exe>
- Lancer, accepter les options par défaut, valider le chemin vers `php.exe` (`C:\xampp\php\php.exe`)
- Redémarrer le terminal et vérifier :
  ```bat
  composer --version
  ```

### 4.  Installer Git pour Windows

- Télécharger : <https://git-scm.com/download/win>
- Installer avec les options par défaut

### 5.  Cloner le projet

Ouvrir **Git Bash** (livré avec Git) ou **PowerShell** :
```bash
git clone https://github.com/HedyMalou/Projet_Dev_Web.git
cd Projet_Dev_Web/laravel_app
composer install
```

---

## Installation pas à pas — macOS

### 1.  Installer Homebrew (gestionnaire de paquets pour macOS)

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### 2.  Installer PHP, Composer, MySQL et Git

```bash
brew install php composer mysql git
brew services start mysql
```

Vérifier :
```bash
php --version
composer --version
mysql --version
```

### 3.  Cloner et installer le projet

```bash
git clone https://github.com/HedyMalou/Projet_Dev_Web.git
cd Projet_Dev_Web/laravel_app
composer install
```

---

## Configuration de la base de données

### 1.  Créer la base de données `stages_cytech`

**Linux / macOS** :
```bash
sudo mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS stages_cytech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Windows (XAMPP)** :
- Ouvrir <http://localhost/phpmyadmin>
- Cliquer sur *Nouvelle base de données*
- Nom : `stages_cytech`, interclassement : `utf8mb4_unicode_ci`
- *Créer*

### 2.  Adapter le fichier `.env`

Le fichier `laravel_app/.env` est déjà préconfiguré pour un environnement local.
Modifiez uniquement les **identifiants MySQL** si nécessaire :

```env
DB_USERNAME=root           # ou le nom d'utilisateur MySQL choisi
DB_PASSWORD=               # vide pour XAMPP par défaut, sinon votre mot de passe
```

> **XAMPP** : par défaut `DB_USERNAME=root` et `DB_PASSWORD=` (vide).
> **Linux** : créez un utilisateur dédié si besoin :
> ```bash
> sudo mysql -u root -p
> CREATE USER 'cytech'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
> GRANT ALL PRIVILEGES ON stages_cytech.* TO 'cytech'@'localhost';
> FLUSH PRIVILEGES;
> EXIT;
> ```

### 3.  Générer la clé applicative (si nécessaire)

```bash
php artisan key:generate
```

> *Cette commande n'est nécessaire que si la ligne `APP_KEY=` du `.env` est vide.*

### 4.  Préparer les dossiers de stockage

**Linux / macOS** :
```bash
mkdir -p storage/app/uploads storage/app/conventions storage/logs
chmod -R 775 storage bootstrap/cache
```

**Windows** : aucune action nécessaire.

### 5.  Lancer les migrations et le jeu de données initial

```bash
php artisan migrate:fresh --seed
```

Cette commande :
- supprime toutes les tables existantes,
- recrée toutes les tables (18 au total),
- insère les utilisateurs et offres de test (voir [Comptes de test](#comptes-de-test)).

---

## Lancement de l'application

```bash
php artisan serve
```

Ouvrir un navigateur sur : **<http://localhost:8000>**

Pour arrêter le serveur : `Ctrl + C`.

---

## Comptes de test

Tous les comptes ci-dessous sont créés automatiquement par le seeder.

| Rôle         | Email                          | Mot de passe |
|--------------|--------------------------------|--------------|
| Admin        | `admin@cytech.fr`              | `admin1234`  |
| Étudiant     | `hedy.ouerghi@etu.cyu.fr`      | `test1234`   |
| Étudiant     | `jeremy.garra@etu.cyu.fr`      | `test1234`   |
| Tuteur       | `titouan.ancelin@etu.cyu.fr`   | `test1234`   |
| Jury         | `sophie.martin@etu.cyu.fr`     | `test1234`   |
| Entreprise   | `contact@techcorp.fr`          | `test1234`   |

> Les étudiants et l'admin sont **directement actifs**.
> Les comptes tuteur, jury et entreprise nouvellement inscrits doivent être **validés par l'admin** avant de pouvoir se connecter.

---

## Récupération du code de double authentification (2FA) en local

En environnement de développement, **les emails ne sont pas envoyés via SMTP** mais écrits dans un fichier de log Laravel. Deux moyens pour récupérer le code :

### Option 1 — Lire le log Laravel

```bash
tail -n 100 storage/logs/laravel.log
```

Le code apparaît dans le contenu de l'email simulé.

### Option 2 — Lire directement la base

```bash
mysql -u <utilisateur> -p stages_cytech \
  -e "SELECT code, date_expiration FROM AUTH_CODE ORDER BY id DESC LIMIT 1;"
```

Sous **XAMPP / phpMyAdmin** : ouvrir la table `AUTH_CODE`, le dernier code généré est en haut.

---

## Structure du projet

```
Projet_Dev_Web/
├── GI_Groupe2_Groupe11.pdf          # Rapport final (livrable 4)
├── README.md                        # Ce fichier
└── laravel_app/                     # Application Laravel
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/         # 7 contrôleurs (Auth, Etudiant, Entreprise, Tuteur, Jury, Admin, Dashboard)
    │   │   └── Middleware/          # CheckAuth + LogActivite
    │   └── Models/                  # 16 modèles Eloquent
    ├── database/
    │   ├── migrations/              # 22 migrations
    │   └── seeders/                 # DatabaseSeeder.php
    ├── resources/
    │   └── views/                   # Vues PHP par rôle (auth, etudiant, entreprise, tuteur, jury, admin, layouts)
    ├── routes/
    │   └── web.php                  # Routes regroupées par middleware d'authentification
    ├── storage/
    │   └── app/
    │       ├── uploads/             # CV, lettres, rapports, fiches d'évaluation
    │       └── conventions/         # Conventions signées
    ├── public/
    │   └── index.php                # Point d'entrée HTTP
    ├── .env                         # Configuration locale (à adapter)
    ├── artisan                      # CLI Laravel
    └── composer.json                # Dépendances PHP
```

---

## Dépannage

### Erreur : `SQLSTATE[HY000] [1045] Access denied for user`
Identifiants MySQL incorrects dans `.env`. Adapter `DB_USERNAME` et `DB_PASSWORD`.

### Erreur : `SQLSTATE[HY000] [2002] Connection refused`
Le serveur MySQL n'est pas démarré.
- Linux : `sudo systemctl start mysql`
- Windows (XAMPP) : démarrer MySQL depuis le panneau de contrôle XAMPP
- macOS : `brew services start mysql`

### Erreur : `The stream or file ... could not be opened in append mode`
Permissions insuffisantes sur le dossier `storage/`.
```bash
chmod -R 775 storage bootstrap/cache
```

### Erreur : `Class "PDO" not found` ou extension manquante
Une extension PHP requise n'est pas installée :
```bash
sudo apt install -y php-mysql php-mbstring php-xml php-curl php-zip
```

### Erreur : `Failed to open stream: Permission denied`
```bash
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Le port 8000 est déjà utilisé
```bash
php artisan serve --port=8080
```
puis se connecter sur <http://localhost:8080>.

### Réinitialiser entièrement la base
```bash
php artisan migrate:fresh --seed
rm -rf storage/app/uploads/* storage/app/conventions/*
```

---

## Documentation complémentaire

Le rapport complet du livrable 4 est disponible à la racine du dépôt :
**`GI_Groupe2_Groupe11.pdf`**

Il détaille l'architecture technique, le modèle de données, les fonctionnalités implémentées par rôle, les choix techniques retenus et le bilan du projet.
