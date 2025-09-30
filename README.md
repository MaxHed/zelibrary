## Zelibrary

Monorepo contenant un backend Symfony et un frontend Nuxt. Ce guide couvre l’installation, le lancement en local, et des conseils de dépannage. (Note: la prise en charge Docker a été retirée de ce guide. Je ne maîtrise pas assez Docker pour proposer un montage fiable, il est donc volontairement exclu.)

### Prérequis
- **PHP** ≥ 8.2 avec **Composer**
- **Symfony CLI** (recommandé pour le serveur local)
- **Node.js** ≥ 18 et **npm**

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
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### Variables d’environnement
Copiez le fichier d’exemple si nécessaire et ajustez vos paramètres (BDD, APP_ENV, etc.).
```bash
cp .env .env.local
```

### Lancer le serveur de développement
Lancer le serveur de développement en utilisant le port 8000
L'option --no-tls est utilisée pour éviter les problèmes de certificats SSL. (sinon le frontend ne peut pas accéder au backend)

```bash
symfony serve --no-tls --port 8000
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

---

## Gestion du projet (synthèse)

- Backend (Symfony + API Platform):
  - Endpoints principaux: `/books`, `/books/{id}`, `/me/books-collection`, `/me/add-book-to-my-collection/{book}`, `/me/delete-book-from-my-collection/{book}`, `/me/is-book-in-my-collection/{book}`, `/reviews`, `/reviews/books/{id}`.
  - Authentification JWT avec cookies HTTP-only (LexikJWT). Appels côté frontend avec `credentials: include`.
  - Données initiales via fixtures; commandes utiles: `app:create-example-users`, `doctrine:fixtures:load`.

- Frontend (Nuxt 4 + TypeScript + Tailwind):
  - Pages: liste des livres, détail (ajout à la collection, reviews), collection personnelle (suppression avec modal), login.
  - `useApi.ts` centralise les appels HTTP avec baseURL `/api` et proxy dev vers `http://localhost:8000`.
  - `ResourceCollection.vue` fournit recherche + pagination génériques.

- Démarrage local recommandé:
  - Backend: `symfony serve` (port 8000 par défaut)
  - Frontend: `npm run dev` (Nuxt sur 3000, proxy `/api` vers 8000)

- Note sur Docker: non couvert volontairement ici. 
