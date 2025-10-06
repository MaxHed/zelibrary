# 🧪 Tests Automatisés - Guide Complet

Ce document décrit l'ensemble des tests automatisés du projet ZeLibrary, leur installation, leur exécution et leur maintenance.

## 📋 Table des matières

- [Vue d'ensemble](#vue-densemble)
- [Installation et Configuration](#installation-et-configuration)
- [Structure des Tests](#structure-des-tests)
- [Exécution des Tests](#exécution-des-tests)
- [Tests Implémentés](#tests-implémentés)
- [Bonnes Pratiques](#bonnes-pratiques)
- [Dépannage](#dépannage)

---

## Vue d'ensemble

### Technologies utilisées

- **PHPUnit 12.4** : Framework de tests unitaires et fonctionnels
- **Symfony WebTestCase** : Tests d'intégration API
- **Doctrine Fixtures** : Données de test
- **JWT sans passphrase** : Authentification en environnement de test

### Couverture actuelle

**31 tests, 58 assertions (100% de succès)**

- ✅ **5 tests** d'authentification
- ✅ **8 tests** de gestion de collection de livres
- ✅ **6 tests** du Repository personnalisé
- ✅ **6 tests** de réinitialisation de mot de passe
- ✅ **6 tests** de gestion des avis/reviews

---

## Installation et Configuration

### 1. Installer les dépendances

Les dépendances de test sont déjà dans `composer.json` :

```json
"require-dev": {
    "phpunit/phpunit": "^12.4",
    "symfony/test-pack": "^1.1"
}
```

Si nécessaire, installez-les :

```bash
cd backend
composer install
```

### 2. Configuration de l'environnement de test

#### a) Fichier `.env.test`

Le fichier `.env.test` contient la configuration pour l'environnement de test :

```env
APP_ENV=test
APP_SECRET=test-secret-key-for-phpunit-tests

# Base de données de test (séparée de dev)
DATABASE_URL="mysql://root:@127.0.0.1:3306/zelibrary_test?serverVersion=8.0.32&charset=utf8mb4"

# JWT pour les tests (SANS passphrase)
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=

# CORS pour les tests
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

#### b) Fichier `.env.test.local` (optionnel)

Si vous avez besoin de surcharger certaines valeurs :

```env
# Configuration locale pour les tests
JWT_PASSPHRASE=
```

### 3. Créer la base de données de test

```bash
cd backend

# Créer la base de données
php bin/console --env=test doctrine:database:create

# Exécuter les migrations
php bin/console --env=test doctrine:migrations:migrate --no-interaction

# Charger les fixtures
php bin/console --env=test doctrine:fixtures:load --no-interaction
```

### 4. Générer les clés JWT sans passphrase

**Important** : Les tests nécessitent des clés JWT **sans passphrase**.

```bash
cd backend

# Supprimer les anciennes clés (si nécessaire)
rm config/jwt/*.pem

# Générer de nouvelles clés SANS passphrase
php bin/console lexik:jwt:generate-keypair --overwrite
# Quand il demande la passphrase, appuyez sur Entrée (laissez vide)
```

### 5. Configuration PHPUnit

Le fichier `phpunit.xml.dist` est déjà configuré :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         backupGlobals="false"
         colors="true"
         bootstrap="tests/bootstrap.php"
>
    <php>
        <ini name="display_errors" value="1" />
        <ini name="error_reporting" value="-1" />
        <server name="APP_ENV" value="test" force="true" />
        <server name="SHELL_VERBOSITY" value="-1" />
        <env name="KERNEL_CLASS" value="App\Kernel" />
    </php>

    <testsuites>
        <testsuite name="Project Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>

    <source>
        <include>
            <directory suffix=".php">src</directory>
        </include>
    </source>
</phpunit>
```

---

## Structure des Tests

```
backend/tests/
├── bootstrap.php              # Bootstrap PHPUnit
├── Api/                       # Tests d'intégration API
│   ├── AuthenticationTest.php # Tests d'authentification
│   ├── BookCollectionTest.php # Tests de collection de livres
│   ├── PasswordResetTest.php  # Tests de reset password
│   └── ReviewTest.php         # Tests des avis/reviews
└── Repository/                # Tests des repositories
    └── BookRepositoryTest.php # Tests du BookRepository
```

---

## Exécution des Tests

### Lancer tous les tests

```bash
cd backend
php bin/phpunit
```

### Avec un affichage détaillé (testdox)

```bash
php bin/phpunit --testdox
```

### Lancer un fichier de test spécifique

```bash
php bin/phpunit tests/Api/AuthenticationTest.php
php bin/phpunit tests/Repository/BookRepositoryTest.php
```

### Lancer un test spécifique

```bash
php bin/phpunit --filter testLoginWithValidCredentials
```

### Avec couverture de code (si xdebug est installé)

```bash
php bin/phpunit --coverage-html coverage/
```

---

## Tests Implémentés

### 1. Authentication (5 tests)

**Fichier** : `tests/Api/AuthenticationTest.php`

| Test | Description |
|------|-------------|
| `testLoginWithValidCredentials` | Vérifie que le login fonctionne avec des credentials valides |
| `testLoginWithInvalidCredentials` | Vérifie que le login échoue avec de mauvais credentials |
| `testAccessProtectedEndpointWithoutAuth` | Vérifie qu'un endpoint protégé retourne 401 sans auth |
| `testAccessProtectedEndpointWithAuth` | Vérifie l'accès à un endpoint protégé avec auth |
| `testLogout` | Vérifie que le logout fonctionne correctement |

**Points clés** :
- Utilise JWT avec cookies HttpOnly
- Teste les codes de statut HTTP (200, 204, 401)
- Vérifie que l'authentification persiste entre les requêtes

### 2. Book Collection (8 tests)

**Fichier** : `tests/Api/BookCollectionTest.php`

| Test | Description |
|------|-------------|
| `testGetMyCollectionRequiresAuth` | Récupération de collection nécessite authentification |
| `testGetMyCollectionWhenAuthenticated` | Récupération réussie quand authentifié |
| `testAddBookToCollectionRequiresAuth` | Ajout nécessite authentification |
| `testAddBookToCollection` | Ajout d'un livre à la collection |
| `testAddNonExistentBookToCollection` | Tentative d'ajout d'un livre inexistant (404) |
| `testDeleteBookFromCollectionRequiresAuth` | Suppression nécessite authentification |
| `testDeleteBookFromCollection` | Suppression d'un livre de la collection |
| `testCheckIfBookIsInCollection` | Vérification de présence d'un livre |

**Points clés** :
- **Utilise `BookRepository::FindOneBookForTest()`** au lieu d'IDs hardcodés
- Teste les codes 200, 401, 404
- Vérifie la logique métier de gestion de collection

**Exemple d'utilisation du Repository** :

```php
private function getBookId($client): int
{
    $container = $client->getContainer();
    $bookRepository = $container->get(BookRepository::class);
    $book = $bookRepository->FindOneBookForTest();
    return $book->getId();
}
```

### 3. Book Repository (6 tests)

**Fichier** : `tests/Repository/BookRepositoryTest.php`

| Test | Description |
|------|-------------|
| `testFindOneBookForTest` | Vérifie que `FindOneBookForTest()` retourne un livre |
| `testFindOneBookNotReviewedByUser` | Teste `findOneBookNotReviewedBy()` |
| `testFindOneBookNotReviewedByUserWithNewUser` | Test avec un utilisateur ayant peu de reviews |
| `testFindOneBookForTestReturnsOnlyOneBook` | Vérifie la cohérence (retourne toujours le même) |
| `testFindOneBookNotReviewedByReturnsNullWhenAllBooksReviewed` | Test du cas limite |
| `testBookFromFindOneBookForTestHasExpectedProperties` | Vérifie l'intégrité de l'objet |

**Points clés** :
- Tests unitaires du repository custom
- Utilise `KernelTestCase` au lieu de `WebTestCase`
- Vérifie la logique SQL personnalisée

**Méthode testée** (corrigée) :

```php
public function findOneBookNotReviewedBy(User $user): ?Book
{
    return $this->createQueryBuilder('b')
        ->leftJoin('b.reviews', 'r', 'WITH', 'r.user = :user')
        ->where('r.id IS NULL')
        ->setParameter('user', $user)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}
```

### 4. Password Reset (6 tests)

**Fichier** : `tests/Api/PasswordResetTest.php`

| Test | Description |
|------|-------------|
| `testOldUnsecureResetPasswordEndpointDoesNotExist` | Ancien endpoint insécurisé n'existe plus |
| `testRequestPasswordResetWithValidEmail` | Demande de reset avec email valide |
| `testRequestPasswordResetWithInvalidEmail` | Demande avec email inexistant (anti-énumération) |
| `testConfirmPasswordResetWithInvalidToken` | Confirmation avec token invalide |
| `testConfirmPasswordResetWithValidToken` | Confirmation réussie avec token valide |
| `testConfirmPasswordResetWithShortPassword` | Validation de la longueur du mot de passe |

**Points clés** :
- Sécurité renforcée (tokens, expiration)
- Anti-énumération (même réponse si email n'existe pas)
- Teste le flux complet de réinitialisation

### 5. Review (6 tests)

**Fichier** : `tests/Api/ReviewTest.php`

| Test | Description |
|------|-------------|
| `testCreateReviewRequiresAuthentication` | Création nécessite authentification |
| `testCreateReviewWithValidData` | Création réussie d'un avis |
| `testCreateReviewWithInvalidRate` | Note invalide (doit être entre 1 et 5) |
| `testCreateReviewWithMissingFields` | Champs obligatoires manquants |
| `testCannotCreateDuplicateReview` | Contrainte d'unicité (1 avis/livre/user) |
| `testDeleteOwnReview` | Suppression de son propre avis |

**Points clés** :
- **Utilise `findOneBookNotReviewedBy()`** pour obtenir des IDs dynamiques
- Teste les validations Symfony (Assert\Range, Assert\NotBlank)
- Vérifie la contrainte d'unicité métier

**Exemple d'utilisation du Repository** :

```php
private function getBookNotReviewedBy($client, string $email): int
{
    $container = $client->getContainer();
    $userRepository = $container->get(UserRepository::class);
    $bookRepository = $container->get(BookRepository::class);
    
    $user = $userRepository->findOneBy(['email' => $email]);
    $book = $bookRepository->findOneBookNotReviewedBy($user);
    
    return $book->getId();
}
```

---

## Bonnes Pratiques

### 1. Pas d'IDs hardcodés

❌ **Mauvais** :
```php
$client->request('POST', '/api/reviews/books/1', ...);
```

✅ **Bon** :
```php
$bookId = $this->getBookNotReviewedBy($client, 'user@email.com');
$client->request('POST', '/api/reviews/books/' . $bookId, ...);
```

### 2. Utiliser les méthodes du Repository

Les tests utilisent les vraies méthodes métier :
- `BookRepository::FindOneBookForTest()`
- `BookRepository::findOneBookNotReviewedBy(User $user)`

Cela garantit que :
- ✅ Les tests sont robustes face aux changements de données
- ✅ La logique métier est testée en conditions réelles
- ✅ Pas de couplage avec les fixtures

### 3. Isolation des tests

Chaque test doit :
- Être indépendant des autres
- Créer ses propres données si nécessaire
- Ne pas dépendre de l'ordre d'exécution

### 4. Nommer les tests clairement

Format recommandé : `test[Action][Condition]`

Exemples :
- `testCreateReviewWithValidData`
- `testAddBookToCollectionRequiresAuth`
- `testCannotCreateDuplicateReview`

### 5. Tester les cas limites

- ✅ Cas nominal (happy path)
- ✅ Cas d'erreur (validation, 404, 401)
- ✅ Cas limites (null, edge cases)

---

## Dépannage

### Erreur : "JWT Token not found" ou "bad decrypt"

**Cause** : Les clés JWT ont été générées avec une passphrase, mais `.env.test` a `JWT_PASSPHRASE=`

**Solution** :
```bash
cd backend
rm config/jwt/*.pem
php bin/console lexik:jwt:generate-keypair --overwrite
# Laissez la passphrase VIDE (appuyez sur Entrée)
```

### Erreur : "Database zelibrary_test does not exist"

**Solution** :
```bash
cd backend
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:migrations:migrate --no-interaction
php bin/console --env=test doctrine:fixtures:load --no-interaction
```

### Erreur : "Book not found" dans les tests

**Cause** : Les fixtures n'ont pas été chargées ou la base est vide

**Solution** :
```bash
cd backend
php bin/console --env=test doctrine:fixtures:load --no-interaction
```

### Les tests sont lents

**Optimisations** :
1. Utilisez une base en mémoire (SQLite) pour les tests
2. Désactivez les logs en environnement de test
3. Utilisez `--stop-on-failure` pour arrêter au premier échec

```bash
php bin/phpunit --stop-on-failure
```

### Nettoyer le cache de test

```bash
cd backend
rm -rf var/cache/test
php bin/console --env=test cache:clear
```

### Réinitialiser complètement la base de test

```bash
cd backend
php bin/console --env=test doctrine:database:drop --force --if-exists
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:migrations:migrate --no-interaction
php bin/console --env=test doctrine:fixtures:load --no-interaction
```

---

## Commandes Utiles

### Tests

```bash
# Tous les tests
php bin/phpunit

# Avec affichage détaillé
php bin/phpunit --testdox

# Un fichier spécifique
php bin/phpunit tests/Api/AuthenticationTest.php

# Un test spécifique
php bin/phpunit --filter testLoginWithValidCredentials

# Arrêt au premier échec
php bin/phpunit --stop-on-failure

# Verbose (affiche les détails)
php bin/phpunit -v
```

### Base de données de test

```bash
# Créer
php bin/console --env=test doctrine:database:create

# Supprimer
php bin/console --env=test doctrine:database:drop --force

# Migrations
php bin/console --env=test doctrine:migrations:migrate --no-interaction

# Fixtures
php bin/console --env=test doctrine:fixtures:load --no-interaction

# Voir le contenu
php bin/console --env=test dbal:run-sql "SELECT COUNT(*) FROM book"
```

### JWT

```bash
# Générer les clés sans passphrase
php bin/console lexik:jwt:generate-keypair --overwrite

# Vérifier les clés
ls -la config/jwt/
```

---

## Maintenance des Tests

### Ajouter un nouveau test

1. Créer le fichier dans `tests/Api/` ou `tests/Repository/`
2. Étendre `WebTestCase` (API) ou `KernelTestCase` (Repository)
3. Suivre la convention de nommage `test[Action][Condition]`
4. Utiliser les repositories pour obtenir des IDs dynamiques

**Exemple** :

```php
<?php

namespace App\Tests\Api;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryTest extends WebTestCase
{
    public function testGetCategories(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/categories');
        
        $this->assertResponseIsSuccessful();
    }
}
```

### Mettre à jour les tests existants

1. Vérifier que les tests passent avant modification
2. Modifier le code
3. Ajuster les tests si nécessaire
4. Vérifier que tous les tests passent

### CI/CD

Pour intégrer les tests dans un pipeline CI/CD :

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql
          
      - name: Install dependencies
        run: |
          cd backend
          composer install --prefer-dist --no-progress
          
      - name: Setup test database
        run: |
          cd backend
          php bin/console --env=test doctrine:database:create
          php bin/console --env=test doctrine:migrations:migrate --no-interaction
          php bin/console --env=test doctrine:fixtures:load --no-interaction
          php bin/console lexik:jwt:generate-keypair --overwrite
          
      - name: Run tests
        run: |
          cd backend
          php bin/phpunit
```

---

## Statistiques Actuelles

```
PHPUnit 12.4.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.3
Configuration: phpunit.xml.dist

OK (31 tests, 58 assertions)
Time: ~2 seconds, Memory: 52 MB
```

### Répartition par domaine

| Domaine | Tests | Assertions |
|---------|-------|------------|
| Authentication | 5 | ~10 |
| Book Collection | 8 | ~16 |
| Book Repository | 6 | 18 |
| Password Reset | 6 | ~8 |
| Review | 6 | ~12 |
| **TOTAL** | **31** | **58** |

---

## Prochaines Étapes

### Tests à ajouter (recommandations)

1. **Tests des autres Repositories**
   - `AuthorRepository`
   - `CategoryRepository`
   - `UserRepository`

2. **Tests de performance**
   - Vérifier l'absence de requêtes N+1
   - Tester la pagination

3. **Tests de sécurité**
   - Tentatives d'accès à des ressources d'autres utilisateurs
   - Tests de rate limiting

4. **Tests fonctionnels**
   - Scénarios utilisateur complets
   - Tests end-to-end

5. **Tests de régression**
   - Ajouter des tests pour chaque bug corrigé

---

## Ressources

- [Documentation PHPUnit](https://phpunit.de/documentation.html)
- [Symfony Testing](https://symfony.com/doc/current/testing.html)
- [API Platform Testing](https://api-platform.com/docs/core/testing/)
- [Doctrine Testing](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/testing.html)

---

**Dernière mise à jour** : 6 octobre 2025  
**Version PHPUnit** : 12.4.0  
**Version Symfony** : 7.3  
**Couverture** : 31 tests, 58 assertions, 100% de succès ✅

