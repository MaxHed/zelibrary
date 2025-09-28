## Zelibrary

Petit monorepo contenant un backend Symfony et un frontend Nuxt. Ce guide couvre l’installation, le lancement en local, l’utilisation de Docker, ainsi que quelques commandes et conseils de dépannage.

### Prérequis
- **PHP** ≥ 8.2 avec **Composer**
- **Symfony CLI** (recommandé pour le serveur local)
- **Node.js** ≥ 18 et **npm**
- (Optionnel) **Docker** et **Docker Compose** pour l’environnement conteneurisé

### Cloner le dépôt
```bash
git clone <url-du-repo>
cd zelibrary
```

## Backend (Symfony)

### Installation des dépendances
```bash
cd backend
composer install
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### Variables d’environnement
Copiez le fichier d’exemple si nécessaire et ajustez vos paramètres (BDD, APP_ENV, etc.).
```bash
cp .env .env.local
```

### Lancer le serveur de développement
```bash
symfony serve
```
Astuce: ajoutez `-d` pour lancer en arrière-plan, et `--no-tls` si vous ne souhaitez pas le HTTPS local.

## Frontend (Nuxt)

### Installation des dépendances
```bash
cd frontend
npm install
```

### Lancer le serveur de développement
```bash
npm run dev
```
Par défaut, Nuxt démarre sur `http://localhost:3000`.

## Scripts utiles

### Backend
- **Démarrer**: `symfony serve`
- **Vider le cache**: `php bin/console cache:clear`
- **Lister les routes**: `php bin/console debug:router`
- **Créer des utilisateurs d'exemple**: `php bin/console app:create-example-users`
- **Importer les Fixtures**: `php bin/console doctrine:fixtures:load`

### Frontend
- **Dev**: `npm run dev`
- **Build**: `npm run build`
- **Preview**: `npm run preview` (selon configuration Nuxt)

## Utilisation avec Docker (optionnel)

### Construire et démarrer
```bash
docker-compose up -d --build
```

### Démarrer/arrêter
```bash
docker-compose up -d
docker-compose down
```

## Structure du projet
```text
zelibrary/
  backend/    # Application Symfony (API/serveur)
  frontend/   # Application Nuxt (UI)
```

## Dépannage
- **Ports occupés**: modifiez le port (`symfony serve -d --port=8001`, ou `.env` Nuxt) si 8000/3000 sont utilisés.
- **Dépendances manquantes**: vérifiez `composer install` et `npm install` dans les dossiers respectifs.
- **Variables d’environnement**: confirmez vos `.env`/`.env.local` côté Symfony et les variables côté Nuxt.
- **Cache Symfony**: `php bin/console cache:clear` peut résoudre certains soucis en dev.
