# 🧪 Suite de Tests ZeLibrary

## Vue d'ensemble

Suite de tests automatisés couvrant les fonctionnalités critiques du backend.

### Tests implémentés

1. **AuthenticationTest** - Authentification JWT
2. **ReviewTest** - Système d'avis
3. **BookCollectionTest** - Collection personnelle
4. **PasswordResetTest** - Reset password sécurisé

---

## 📋 Prérequis

```bash
# Installer PHPUnit
composer require --dev phpunit/phpunit symfony/test-pack

# Créer la base de données de test
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-interaction
```

---

## 🚀 Lancer les tests

### Tous les tests

```bash
php bin/phpunit
```

### Tests spécifiques

```bash
# Authentification uniquement
php bin/phpunit tests/Api/AuthenticationTest.php

# Reviews uniquement
php bin/phpunit tests/Api/ReviewTest.php

# Collection de livres
php bin/phpunit tests/Api/BookCollectionTest.php

# Reset password
php bin/phpunit tests/Api/PasswordResetTest.php
```

### Avec couverture de code

```bash
XDEBUG_MODE=coverage php bin/phpunit --coverage-html var/coverage
```

---

## 📊 Tests par catégorie

### 🔐 AuthenticationTest (6 tests)

| Test | Description | Vérifie |
|------|-------------|---------|
| `testLoginWithValidCredentials` | Login réussi | Cookie JWT créé, HttpOnly |
| `testLoginWithInvalidCredentials` | Login échoué | Retourne 401 |
| `testAccessProtectedEndpointWithoutAuth` | Accès sans auth | Retourne 401 |
| `testAccessProtectedEndpointWithAuth` | Accès avec auth | Retourne les données user |
| `testLogout` | Déconnexion | Cookie supprimé |

**Couverture :**
- ✅ JWT fonctionnel
- ✅ Cookie HttpOnly
- ✅ Endpoints protégés
- ✅ Logout sécurisé

---

### ⭐ ReviewTest (6 tests)

| Test | Description | Vérifie |
|------|-------------|---------|
| `testCreateReviewRequiresAuthentication` | Review sans auth | Retourne 401 |
| `testCreateReviewWithValidData` | Création valide | Retourne 201 |
| `testCreateReviewWithInvalidRate` | Note invalide | Retourne 422 |
| `testCreateReviewWithMissingFields` | Champs manquants | Retourne 422 |
| `testCannotCreateDuplicateReview` | Contrainte unicité | Retourne 409 |
| `testDeleteOwnReview` | Suppression | Retourne 204 |

**Couverture :**
- ✅ Authentification requise
- ✅ Validation (rate 1-5)
- ✅ **Contrainte unicité critique**
- ✅ Codes HTTP corrects

---

### 📚 BookCollectionTest (7 tests)

| Test | Description | Vérifie |
|------|-------------|---------|
| `testGetMyCollectionRequiresAuth` | Liste sans auth | Retourne 401 |
| `testGetMyCollectionWhenAuthenticated` | Liste avec auth | Retourne collection |
| `testAddBookToCollectionRequiresAuth` | Ajout sans auth | Retourne 401 |
| `testAddBookToCollection` | Ajout valide | Retourne 200 |
| `testAddNonExistentBookToCollection` | Livre inexistant | Retourne 404 |
| `testDeleteBookFromCollectionRequiresAuth` | Suppression sans auth | Retourne 401 |
| `testDeleteBookFromCollection` | Suppression valide | Retourne 204 |
| `testCheckIfBookIsInCollection` | Vérification présence | Retourne statut |

**Couverture :**
- ✅ CRUD complet de la collection
- ✅ Sécurité des endpoints
- ✅ Gestion des erreurs 404

---

### 🔒 PasswordResetTest (6 tests)

| Test | Description | Vérifie |
|------|-------------|---------|
| `testOldUnsecureResetPasswordEndpointDoesNotExist` | Ancien endpoint | **Doit être 404/405** |
| `testRequestPasswordResetWithValidEmail` | Demande valide | Token généré |
| `testRequestPasswordResetWithInvalidEmail` | Email inexistant | **Même message (anti-énumération)** |
| `testConfirmPasswordResetWithInvalidToken` | Token invalide | Retourne 400 |
| `testConfirmPasswordResetWithValidToken` | Reset valide | Password changé |
| `testConfirmPasswordResetWithShortPassword` | Password trop court | Retourne 400 |

**Couverture :**
- ✅ **Ancien endpoint dangereux retiré**
- ✅ Système de token sécurisé
- ✅ Protection anti-énumération
- ✅ Validation password

---

## 🎯 Résultats attendus

### Score de couverture cible

- **Authentification :** 90%+
- **Reviews :** 85%+
- **Collection :** 85%+
- **Reset Password :** 80%+
- **Global :** 70%+

### Temps d'exécution

- **Suite complète :** ~10-15 secondes
- **Tests rapides uniquement :** ~5 secondes

---

## 🔧 Configuration

### phpunit.xml.dist

```xml
- Environment de test (APP_ENV=test)
- Bootstrap via tests/bootstrap.php
- Couverture de code sur src/
```

### Base de données de test

Les tests utilisent une base séparée configurée dans `.env.test` :

```bash
DATABASE_URL="mysql://root:@127.0.0.1:3306/zelibrary_test?serverVersion=8.0.32&charset=utf8mb4"
```

---

## 🐛 Dépannage

### Erreur : "Table 'zelibrary_test' doesn't exist"

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-interaction
```

### Erreur : "Class 'PHPUnit\Framework\TestCase' not found"

```bash
composer require --dev phpunit/phpunit
```

### Tests échouent avec JWT

```bash
# Régénérer les clés JWT
php bin/console lexik:jwt:generate-keypair --overwrite

# Vérifier .env.test
grep JWT .env.test
```

---

## 📈 Ajout de nouveaux tests

### Template de test

```php
<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MyFeatureTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testMyFeature(): void
    {
        $this->client->request('GET', '/api/my-endpoint');
        $this->assertResponseIsSuccessful();
    }
}
```

---

## ✅ Checklist CI/CD

- [ ] Tests lancés automatiquement sur chaque commit
- [ ] Pipeline échoue si tests échouent
- [ ] Couverture de code mesurée
- [ ] Rapport de tests archivé
- [ ] Notifications des échecs

---

## 📚 Ressources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Symfony Testing](https://symfony.com/doc/current/testing.html)
- [API Platform Testing](https://api-platform.com/docs/core/testing/)

---

**Date de création :** 6 octobre 2025  
**Couverture actuelle :** 25 tests critiques  
**Status :** ✅ Suite de tests fonctionnelle

