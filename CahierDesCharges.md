# Cahier des Charges `ZeLibrary`
---
## 1. Description

### 1.1 Concept de base
`ZeLibrary` est une application de gestion de bibliothèque. Cette application permet aux utilisateurs de gérer leurs livres, faire des reviews et des notes sur les livres.

### 1.2 Idées d'évolution
On pourrait imaginer que les bibliothèques utilisent cette application pour gérer leurs livres, proposer des reservations de livres, etc.

### 1.3 Idées d'évolution (Hors scope)
Si on pousse un peu plus loin l'idée, les utilisateurs pourraient vendre/troquer leurs livres avec d'autres utilisateurs.

---
## 2. Différents types d'utilisateurs

### 2.1 Utilisateurs
- Utilisateur lambda (un lecteur) : `User`
- Utilisateur bibliothécaire (un employé d'une bibliothèque) : `LibraryEmployee`
- Utilisateur administrateur (un administrateur de la plateforme) : `Admin`

### 2.2 User Stories
#### 2.2.1 Utilisateur lambda
Un utilisateur lambda peut :
- Se connecter
- Se déconnecter
- Ajouter un livre à sa bibliothèque
- Supprimer un livre de sa bibliothèque
- Rechercher un livre
- Afficher la liste des livres
- Afficher un livre
- Afficher la liste des utilisateurs
- Afficher un utilisateur
- Trouver un livre disponible à l'emprunt dans une bibliothèque
- Demander à réserver un livre

#### 2.2.2 Utilisateur bibliothécaire
Un utilisateur bibliothécaire peut :
- Gérer les utilisateurs
- Gérer les livres
- Gérer les bibliothèques
- Gérer les emprunts
- Gérer les réservations
- Gérer les reviews
- Gérer les notes

#### 2.2.3 Utilisateur administrateur
Un utilisateur administrateur peut :
- Gérer les utilisateurs
- Gérer les livres
- Gérer les bibliothèques
- Gérer les emprunts
- Gérer les réservations
- Gérer les reviews
- Gérer les notes


### 2.3 Entities
#### 2.3.1 Entity User
##### Détail de l'entité
```
Nom de l'entité : User
- id : int
- email : string
- password : string
- roles : array
- collection : relation avec Collection
```
##### Stories
- Un utilisateur peut avoir plusieurs rôles
- Un utilisateur peut avoir une bibliothèque personnel (Collection)

#### 2.3.2 Entity Book
##### Détail de l'entité
```
Construit sur la base du modèle de Gutendex
Nom de l'entité : Book
- id : int
- title : string
- summary : text
- languages : array
- copyright : boolean
- mediaType : string
- formats : array
- downloadCount : int
- gutendexId : int
- createdAt : datetime
- updatedAt : datetime
```
##### Stories 
- Un livre peut appartenir à quelqu'un (dans une bibliothèque personnel)
- Un livre peut appartenir à une bibliothèque (dans une bibliothèque officielle)
- Un livre peut être emprunté par quelqu'un (uniquement dans une bibliothèque officielle)
- Un livre peut être réservé par quelqu'un (uniquement dans une bibliothèque officielle)
- Un livre peut être retourné par quelqu'un (uniquement dans une bibliothèque officielle)


#### 2.3.3 Entity Library
Library représente une bibliothèque officielle. 
##### Détail de l'entité
```
Nom de l'entité : Library
- id : int
- name : string
- address : string
- phone : string
- email : string
- logo : string
- website : string
- description : text
- borrowLimit : int
- booksCollection : relation avec Book (Many to Many)
- employees : relation avec User (One to Many)
- createdAt : datetime
- updatedAt : datetime
```
##### Stories 
- Une bibliothèque peut avoir plusieurs livres (dans une collection)
- Une bibliothèque peut avoir plusieurs emprunteurs
- Une bibliothèque peut avoir plusieurs emprunts


#### 2.3.5 Entity Borrow
Borrow représente un emprunt d'un livre par un utilisateur.
##### Détail de l'entité
```
Nom de l'entité : Borrow
- id : int
- book : relation avec Book
- user : relation avec User
- library : relation with Library
- status : string
- createdAt : datetime
- updatedAt : datetime
```

##### Stories
- Un emprunt peut avoir un statut :
    - Reservation en attente de validation,
    - Reservé,
    - En cours,
    - terminé,
    - en retard,
    - perdu,
    - volé,
    - restitué
- Un emprunt peut avoir une date d'emprunt
- Un emprunt peut avoir une date de retour


#### 2.3.6 Entity Review
Review représente une review d'un livre par un utilisateur.
##### Détail de l'entité
```
Nom de l'entité : Review
- id : int
- book : relation avec Book
- user : relation avec User
- review : text
- rate : int
- createdAt : datetime
- updatedAt : datetime
```
##### Stories
- Une review peut avoir un commentaire
- Une review peut avoir une note


## 3. Fonctionnalités
### 3.1 Fonctionnalités communes
- Gestion des utilisateurs
- Gestion des livres
- Gestion des bibliothèques
- Gestion des emprunts
- Gestion des réservations
- Gestion des reviews
- Gestion des notes

### 3.2 Fonctionnalités spécifiques

### 3.3 Boite à idées
- Utiliser une API de livres pour récupérer les informations des livres
- Utiliser l'API OpenAI pour analyser la demande d'un utilisateur qui cherche un livre selon sa demande, puis retourner une liste de livres possibles, checker si on a le livre dans la base de donnée, autrement, utiliser l'API de livres.

---
## 4. Authentification (SPA) et Sécurité

### 4.1 Architecture choisie
- L'application Front (Nuxt en mode SPA) appelle directement l'API Symfony (API Platform) sans serveur Nuxt en intermédiaire.
- Authentification via un cookie JWT `AUTH_TOKEN` émis par le backend (LexikJWT): `HttpOnly`, `Secure`, `SameSite=None` (pour autoriser un envoi cross‑site en HTTPS).
- Aucun secret/jeton n'est stocké dans le navigateur côté JS. Seul un petit état d'UI (ex: `isAuth`, `user`) peut être gardé en `localStorage`.

### 4.2 Endpoints d'authentification
- `POST /api/login` (backend émet le cookie `AUTH_TOKEN` dans la réponse en cas de succès)
- `GET /api/me` (récupère le profil de l'utilisateur authentifié)
- `POST /api/logout` (backend efface le cookie `AUTH_TOKEN`)

Notes:
- Le backend invalide/efface aussi le cookie en cas de JWT invalide/expiré via un subscriber dédié.
- Le TTL du token est de 3600s (config LexikJWT), aligné avec l'expiration du cookie.

### 4.3 Configuration CORS et cookies
- Côté Symfony (NelmioCors):
  - `allow_credentials: true`, origines autorisées explicites (localhost/127.0.0.1:3000 en dev).
  - Méthodes/Headers autorisés définis, gestion d'`OPTIONS` activée.
- Côté cookie:
  - `SameSite=None` pour un SPA cross‑site; nécessite `Secure=true` et donc HTTPS côté backend.
  - En dev, utiliser `https://127.0.0.1:8000` pour l'API (cert local accepté).

### 4.4 Règles d’appel côté Front (SPA)
- Base API: `runtimeConfig.public.apiBase = "https://127.0.0.1:8000/api"` (dev).
- Toujours appeler avec `credentials: 'include'` pour envoyer le cookie.
- Ne pas ajouter d'en‑têtes personnalisés (ex: `Authorization`) côté client pour éviter les preflight inutiles.
- Collections API Platform: ajouter un slash final (ex: `/api/books/`) pour éviter une redirection 301 susceptible de casser le preflight.

Exemple (conceptuel):
```
$fetch(`${apiBase}/books/`, { credentials: 'include' })
```

### 4.5 Stockage et restauration d’état côté Front
- Ne pas stocker le JWT en `localStorage` (cookie HttpOnly déjà géré par le navigateur).
- Optionnel: stocker `isAuth` ("1"/"0") et `user` (profil sérialisé) en `localStorage` pour accélérer l’UX.
- Au chargement de l’app, restaurer l’état, puis vérifier/rafraîchir via `GET /api/me`.

### 4.6 Sécurité complémentaire
- Limiter strictement les origines CORS en prod (pas de `*` avec credentials).
- Envisager un anti‑CSRF si des requêtes mutatives sont fréquentes et si l’app s’ouvre sur plusieurs origines (double‑submit token ou header custom validé côté serveur). Avec JWT + CORS strict et origines contrôlées, le risque est réduit mais à évaluer selon la surface d’attaque.
- Journaliser les erreurs d’auth et retenter prudemment pour les actions idempotentes.

### 4.7 Impacts et responsabilités
- Backend:
  - Émet le cookie `AUTH_TOKEN` (`HttpOnly`, `Secure`, `SameSite=None`), le nettoie à l’expiration/invalidation, et maintient une config CORS stricte.
  - Fournit `login`, `logout`, `me`, et protège les routes `/api/*` via JWT.
- Frontend:
  - Appelle directement l’API via HTTPS, `credentials: 'include'`, gère les états d’UI en localStorage, et n’expose jamais le token.

### 4.8 Composable `useApi` (centralisation des appels)
- Fichier: `app/composable/useApi.ts`
- Rôle: fournir une fonction unique `apiCall(method, endpoint, data?, options?)` pour éviter la duplication et normaliser les appels.
- Base URL: `runtimeConfig.public.apiBase` (ex: `https://127.0.0.1:8000/api`).
- Par défaut: `credentials: 'include'` (envoi du cookie `AUTH_TOKEN`). Pas de headers custom pour éviter des preflight CORS inutiles.

API
```
apiCall<T = any>(
  method: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE',
  endpoint: string,              // ex: '/books/'
  data?: any,                    // corps JSON pour POST/PUT/PATCH
  options?: {
    params?: Record<string, any> // querystring ex: { page: 2 }
    headers?: Record<string, string>
    credentials?: RequestCredentials // override si besoin
  }
): Promise<T>
```

Exemples
```
// GET avec paramètres
const books = await apiCall('GET', '/books/', undefined, { params: { page: 2 } })

// POST JSON
await apiCall('POST', '/reviews', { book: 1, rate: 5, comment: 'Top' })

// PATCH
await apiCall('PATCH', '/books/123', { title: 'Nouveau titre' })

// DELETE
await apiCall('DELETE', '/books/123')
```

Intégration dans un composable métier
```
// app/composable/useBooks.ts (extrait)
const { apiCall } = useApi()
const fetchBooks = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await apiCall<any>('GET', '/books/')
    const member = res['hydra:member'] ?? []
    books.value = member
    totalItems.value = res['hydra:totalItems'] ?? member.length
  } catch (e: any) {
    error.value = e?.message ?? 'Erreur de chargement'
  } finally {
    loading.value = false
  }
}
```

Notes
- Toujours préfixer `endpoint` par `/` (il est concaténé à `apiBase`).
- Pour les collections API Platform, conserver le slash final (`/books/`) afin d’éviter une redirection 301.
- Les erreurs de `$fetch` remontent sous forme d’exceptions; gérer `try/catch` côté composables pour fournir des messages clairs à l’UI.
