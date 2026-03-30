# Installation Laravel — Plateforme Stages CY Tech

## 1. Créer le projet Laravel dans ce dossier

```bash
cd /home/cytech/Projet_Dev_Web
composer create-project laravel/laravel laravel_app
cd laravel_app
```

> Les fichiers `app/Models/` et `database/migrations/` du dépôt remplacent
> ceux générés par Laravel. Copiez-les après la création.

## 2. Configurer la base de données (.env)

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stages_cytech
DB_USERNAME=hedy
DB_PASSWORD=votre_mot_de_passe

APP_NAME="Plateforme Stages"
APP_URL=http://localhost/Projet_Dev_Web/laravel_app/public
```

## 3. Lancer les migrations

```bash
# Option A — base de données vierge (recommandé pour la démo)
php artisan migrate --seed

# Option B — si vous avez déjà la DB du schema.sql importée
# (pas besoin de migrate, les tables existent déjà)
php artisan db:seed
```

## 4. Lancer le serveur de développement

```bash
php artisan serve
# → http://127.0.0.1:8000
```

## 5. Permissions du dossier uploads (fichiers déposés par les étudiants)

```bash
mkdir -p storage/app/uploads
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

## Tables créées (12)

| Migration | Table | Dépend de |
|-----------|-------|-----------|
| 000001 | UTILISATEUR | — |
| 000002 | AUTH_CODE | UTILISATEUR |
| 000003 | ETUDIANT | UTILISATEUR |
| 000004 | TUTEUR | UTILISATEUR |
| 000005 | JURY | UTILISATEUR |
| 000006 | ENTREPRISE | UTILISATEUR |
| 000007 | OFFRE_STAGE | ENTREPRISE |
| 000008 | CANDIDATURE | ETUDIANT, OFFRE_STAGE |
| 000009 | CONVENTION | CANDIDATURE |
| 000010 | DOCUMENT | CANDIDATURE |
| 000011 | SUIVI | TUTEUR, CANDIDATURE |
| 000012 | COMMENTAIRE | CANDIDATURE, UTILISATEUR |
