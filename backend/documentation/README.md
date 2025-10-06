# 📚 Documentation ZeLibrary - Backend

Ce dossier contient toute la documentation technique du backend ZeLibrary.

## 📑 Documents Disponibles

### 🔒 Sécurité

- **[CORS_CONFIGURATION.md](CORS_CONFIGURATION.md)**  
  Configuration du CORS (Cross-Origin Resource Sharing) pour permettre les requêtes du frontend.  
  - Configuration dynamique via variable d'environnement
  - Support multi-environnements (dev, test, prod)
  - Exemples de configuration

- **[PASSWORD_RESET_GUIDE.md](PASSWORD_RESET_GUIDE.md)**  
  Guide complet du système de réinitialisation de mot de passe sécurisé.  
  - Flux de réinitialisation avec tokens temporaires
  - Protection anti-énumération
  - Exemples d'utilisation et de test
  - Configuration de l'envoi d'emails

### 🧪 Tests

- **[TESTS.md](TESTS.md)**  
  Documentation complète des tests automatisés du projet.  
  - Installation et configuration de l'environnement de test
  - Structure des tests (31 tests, 58 assertions)
  - Guide d'exécution et bonnes pratiques
  - Utilisation des méthodes du Repository (pas d'IDs hardcodés)
  - Dépannage et maintenance

## 🚀 Démarrage Rapide

### Pour développer

1. Consultez le [README.md](../README.md) principal du backend
2. Configurez le CORS : [CORS_CONFIGURATION.md](CORS_CONFIGURATION.md)
3. Lancez les tests : [TESTS.md](TESTS.md)

### Pour comprendre la sécurité

1. Système d'authentification JWT : voir [README.md](../README.md)
2. Réinitialisation de mot de passe : [PASSWORD_RESET_GUIDE.md](PASSWORD_RESET_GUIDE.md)
3. Configuration CORS : [CORS_CONFIGURATION.md](CORS_CONFIGURATION.md)

### Pour contribuer

1. Lisez les conventions dans [README.md](../README.md) section "Qualité & Conventions"
2. Écrivez des tests : [TESTS.md](TESTS.md)
3. Suivez les bonnes pratiques de sécurité

## 📊 État du Projet

### ✅ Fonctionnalités Implémentées

- Authentification JWT avec cookies HttpOnly
- Gestion des utilisateurs (register, login, logout, profil)
- Réinitialisation sécurisée de mot de passe
- Gestion des livres (CRUD, recherche, pagination)
- Collection personnelle de livres par utilisateur
- Système d'avis/reviews avec contrainte d'unicité
- CORS configurable par environnement

### 🧪 Couverture de Tests

- **31 tests PHPUnit** (100% de succès)
- **58 assertions**
- Tests d'intégration API
- Tests unitaires des Repositories
- Tests de sécurité (authentification, validation)

### 🔐 Sécurité

- JWT avec cookies HttpOnly (protection XSS)
- Tokens de réinitialisation avec expiration (1 heure)
- Anti-énumération des emails
- Validation stricte des entrées
- CORS configurable et sécurisé
- `declare(strict_types=1)` sur tous les nouveaux fichiers

## 🛠️ Technologies

- **Symfony 7.3** - Framework PHP
- **API Platform 4** - API REST automatisée
- **Doctrine ORM 3** - Gestion de base de données
- **LexikJWTAuthenticationBundle** - Authentification JWT
- **PHPUnit 12.4** - Tests automatisés
- **MySQL/MariaDB** - Base de données

## 📝 Conventions

### Codes HTTP

- `200 OK` - Lecture réussie
- `201 Created` - Création réussie
- `204 No Content` - Suppression réussie
- `400 Bad Request` - Erreur de validation
- `401 Unauthorized` - Non authentifié
- `403 Forbidden` - Non autorisé
- `404 Not Found` - Ressource inexistante
- `409 Conflict` - Conflit (ex: review déjà existante)
- `422 Unprocessable Entity` - Validation échouée

### Structure des Fichiers

```
backend/
├── bin/
│   └── console              # Console Symfony
├── config/
│   ├── packages/            # Configuration des bundles
│   ├── routes/              # Routes
│   └── jwt/                 # Clés JWT
├── documentation/           # 📚 Documentation (ce dossier)
│   ├── README.md            # Index de la documentation
│   ├── CORS_CONFIGURATION.md
│   ├── PASSWORD_RESET_GUIDE.md
│   └── TESTS.md
├── migrations/              # Migrations Doctrine
├── public/
│   └── index.php            # Point d'entrée
├── src/
│   ├── Controller/          # Contrôleurs API
│   ├── Entity/              # Entités Doctrine
│   ├── Repository/          # Repositories personnalisés
│   ├── EventSubscriber/     # Subscribers (ex: JWT Cookie)
│   └── DataFixtures/        # Données de test
├── tests/                   # Tests PHPUnit
│   ├── Api/                 # Tests d'intégration
│   └── Repository/          # Tests unitaires
├── vendor/                  # Dépendances Composer
├── .env                     # Variables d'environnement
├── composer.json            # Dépendances PHP
├── phpunit.xml.dist         # Configuration PHPUnit
└── README.md                # README principal
```


**Dernière mise à jour** : 6 octobre 2025  
**Version Symfony** : 7.3  
**Version API Platform** : 4  
**Version PHP** : 8.4.3

