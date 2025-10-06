## Backend – ZeLibrary (Symfony 7.3 + API Platform 4)

Ce dossier contient l’API du projet ZeLibrary, construite avec Symfony 7.3, API Platform 4, Doctrine ORM 3 et LexikJWTAuthenticationBundle.

Prérequis
- PHP >= 8.2
- Composer
- Base de données (MySQL/MariaDB ou PostgreSQL)
- OpenSSL pour les clés JWT

Installation
```
cd backend
composer install
```
 *J'ai laissé les certs JWT dans le projets pour que vous puissiez directement les utiliser, Mais bien évidement qu'il ne faut pas les commit dans un git en temps normal* 

 *Pour le **.env**, vous pouvez directement utilisser celui présent pour les tests*

Démarrage
```
# serveur de dev Symfony
symfony server:start -d --no-tls
# ou
php -S 127.0.0.1:8000 -t public 
```

Base de données:
```
# migrations
php bin/console doctrine:migrations:migrate --no-interaction
# fixtures (optionnel/dev)
php bin/console doctrine:fixtures:load --no-interaction
```

Authentification JWT & Cookies
- Login: POST /api/login_check avec JSON { "email": "...", "password": "..." }
- Le token est émis et placé dans un cookie HttpOnly AUTH_TOKEN via un subscriber (App\EventSubscriber\JWTCookieSubscriber).
- Le firewall api extrait le token depuis le cookie (config lexik_jwt_authentication.yaml).
- Logout: POST /api/logout supprime le cookie (SameSite/secure adaptés selon APP_ENV).

Comportement ENV:
- En dev: cookie SameSite=Lax, Secure=false (HTTP autorisé).
- En prod: SameSite=None, Secure=true (HTTPS requis).

Endpoints principaux
- Utilisateurs
  - GET /api/me – Profil utilisateur courant (auth requis)
  - POST /api/register – Création de compte (email, password)
- Authentification
  - POST /api/login_check – Connexion (retourne cookie JWT)
  - POST /api/logout – Déconnexion (supprime cookie)
- Reset Password (SÉCURISÉ)
  - POST /api/password-reset/request – Demande de reset (génère token)
  - POST /api/password-reset/confirm – Confirmation avec token
  - Voir `PASSWORD_RESET_GUIDE.md` pour les détails
- Livres
  - GET /api/books – Liste paginée (recherche et tri activés)
  - GET /api/books/{id} – Détail d’un livre
- Collection personnelle (utilisateur)
  - POST /api/me/add-book-to-my-collection/{book} – Ajout d’un livre (auth)
  - DELETE /api/me/delete-book-from-my-collection/{book} – Suppression (auth)
- Avis
  - GET /api/reviews – Liste des avis
  - POST /api/reviews/books/{id} – Créer un avis pour un livre (auth, 1 avis par livre/utilisateur)
  - DELETE /api/reviews/{review} – Supprimer mon avis (auth, 204)

API Platform expose également la documentation OpenAPI/Swagger à /api.

Migrations & Fixtures
- Migrations: répertoire migrations/ (Doctrine Migrations)
- Fixtures: répertoire src/DataFixtures/ (dev/test)


Tests Automatisés
- **31 tests PHPUnit**, 58 assertions (100% de succès) ✅
- Tests d'intégration API (authentification, collection, reviews)
- Tests unitaires des Repositories personnalisés
- **Pas d'IDs hardcodés** : utilisation des méthodes du Repository

**Lancer les tests** :
```bash
cd backend
php bin/phpunit --testdox
```

**Documentation complète** : `documentation/TESTS.md`

Tests API/collection Postman (optionnel):
- Fichier: tests/zelibrary.postman_collection.json
- Importez la collection dans Postman et configurez l'URL de base.

Qualité & Conventions
- declare(strict_types=1); sur les nouveaux fichiers.
- Codes HTTP cohérents (ex. 201 pour création, 204 pour suppression).
- Ne pas renvoyer d’entités doctrine directement: sérialiser les champs nécessaires.
- Respecter les contraintes de validation (Assert) côté entités.

Sécurité
- Firewalls: login (json_login), api (stateless + jwt)
- Extraction du JWT via cookie AUTH_TOKEN.
- Pensez à activer HTTPS et SameSite=None en production.
- **Reset password sécurisé** : système de token temporaire avec expiration
- **⚠️ IMPORTANT** : Voir `SECURITY_FIXES.md` pour les correctifs appliqués

Déploiement
- Générer les clés JWT et variables d’environnement.
- Exécuter les migrations DB.
- Configurer le reverse-proxy pour transmettre les en-têtes X-Forwarded-* si nécessaire.

--
Pour toute évolution, mettez à jour ce README et la collection de tests.

