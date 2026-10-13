# VideGrenier+

> Plateforme de dons d'objets entre particuliers à Kinshasa. On publie un objet en photo, les personnes intéressées écrivent directement au donneur sur WhatsApp.

[![CI](https://github.com/gradi08/dan_site/actions/workflows/ci.yml/badge.svg)](https://github.com/gradi08/dan_site/actions/workflows/ci.yml)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-777bb4)
![Dépendances](https://img.shields.io/badge/d%C3%A9pendances%20runtime-0-success)

**Démo :** _à compléter après déploiement_ · Compte de démonstration : `demo@videgrenier.test` / `demo1234`

---

## Fonctionnalités

- **Catalogue public** : recherche plein texte (titre, description, commune), filtres par catégorie, pagination, fiches objets avec balises Open Graph pour le partage.
- **Contact WhatsApp en un clic** avec message pré-rempli. Les numéros ne sont visibles que par les membres connectés (anti-démarchage).
- **Espace donneur** : publier, modifier, passer en brouillon, **marquer comme donné**, supprimer (avec confirmation).
- **Comptes** : inscription, connexion, profil, changement de mot de passe, normalisation des numéros au format international (+243).
- **Interface** responsive (mobile d'abord), thème clair/sombre automatique, accessible au clavier et aux lecteurs d'écran, fonctionne sans JavaScript.

## Architecture

PHP 8.1+ **sans framework**, volontairement : l'objectif est de montrer la maîtrise des fondamentaux qu'un framework masque (routage, cycle requête/réponse, sécurité, accès aux données), tout en restant déployable sur n'importe quel hébergement mutualisé.

```
public/            Racine web : front controller, assets, uploads
src/
  Core/            App (conteneur de services), Database, Session, Csrf, View, Validator, Env
  Http/            Request, Response, Router (middlewares auth/guest, CSRF automatique)
  Controller/      Home, Article, Auth, Dashboard, Profile
  Repository/      Accès aux données (PDO, requêtes préparées uniquement)
  Service/         Auth, LoginThrottle, ImageUploader
  Support/         Helpers de vue, Phone, Category, Status
config/            Configuration et table de routage
views/             Templates PHP (layout + pages + partials)
database/          Schémas MySQL et SQLite, migration depuis la v1
tests/             PHPUnit (30 tests)
```

**Choix notables**

| Sujet | Choix |
| --- | --- |
| Base de données | MySQL/MariaDB en production, **SQLite en local** (zéro configuration). Requêtes portables, schéma InnoDB avec clés étrangères et index. |
| Routage | URL propres (`/objets/12`, `/mes-annonces/nouvelle`), redirections 301 depuis les anciennes URL `*.php`. |
| Formulaires | Pattern Post/Redirect/Get, erreurs et saisies conservées en session flash, validation côté serveur et côté navigateur (`:user-invalid` + `aria-invalid`). |
| Déploiement | Fonctionne avec ou sans Composer (autoloader PSR-4 de secours), avec Apache, le serveur PHP intégré ou Docker. |

## Sécurité

- **CSRF** : jeton vérifié automatiquement sur toute requête POST ; aucune action destructrice en GET.
- **XSS** : échappement systématique en sortie (`e()`), **Content-Security-Policy** stricte (`script-src 'self'`, aucun script inline).
- **SQL** : requêtes préparées partout, émulation désactivée, `LIKE` échappé.
- **Upload** : type MIME détecté sur le contenu réel, image **ré-encodée** avec GD (neutralise tout fichier piégé et supprime les métadonnées EXIF/GPS), redimensionnée, nom aléatoire ; exécution de scripts interdite dans `uploads/`.
- **Authentification** : `password_hash` (bcrypt, rehash automatique), régénération de l'ID de session, cookies `HttpOnly` + `SameSite=Lax` (+ `Secure` en HTTPS), vérification à temps constant contre l'énumération des comptes, **limitation des tentatives** (5 essais / 15 min par e-mail + IP).
- **Contrôle d'accès** : chaque requête sur une annonce vérifie son propriétaire (404 sinon).
- **En-têtes** : `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`.
- Code source, configuration et base de données hors de la racine web.

## Lancer le projet en local

Prérequis : PHP 8.1+ avec les extensions `pdo_sqlite`, `gd` et `fileinfo`.

```bash
cp .env.example .env
php bin/setup.php --seed
php -S localhost:8000 -t public public/index.php
```

Puis ouvrir http://localhost:8000.

Avec Docker (Apache + MariaDB) :

```bash
docker compose up --build
```

L'application est servie sur http://localhost:8080.

## Tests

```bash
composer install
composer test
```

La CI GitHub Actions exécute le lint, les tests sur PHP 8.1 et 8.3, puis un test de fumée HTTP sur le serveur intégré.

## Déploiement

### Hébergement mutualisé (InfinityFree, o2switch, Hostinger…)

1. Créer une base MySQL et importer `database/schema.mysql.sql` via phpMyAdmin.
2. Envoyer tous les fichiers du projet (sans `vendor/`, `tests/`, `.git/`) dans `htdocs/`.
3. Créer un fichier `.env` à partir de `.env.example` avec `APP_ENV=production`, `APP_DEBUG=false`, `DB_DRIVER=mysql` et les identifiants de la base.
4. Si la racine web ne peut pas pointer sur `public/`, le `.htaccess` à la racine s'en charge et bloque l'accès au reste.

**Mise à jour depuis la v1** (anciennes tables `users`/`articles`) : sauvegarder la base, exécuter `database/migrations/001_from_legacy_v1.sql`, puis copier l'ancien dossier `img/` vers `public/uploads/`. Les comptes et mots de passe existants sont conservés.

### Docker (Render, Railway, Fly.io…)

L'image écoute sur `$PORT` si la plateforme le fournit et crée le schéma au démarrage. Variables à définir : `DB_DRIVER=mysql`, `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `APP_DEBUG=false` (et `SEED_DEMO=true` pour une démo). Prévoir un volume persistant monté sur `/var/www/html/public/uploads`.

## Auteur

Conçu et développé par [Gradi N'chaki](https://gradinchaki.vercel.app).
