# Configuration de l'API

## Configuration de l'environnement

1. **Copiez le fichier d'exemple :**
   ```bash
   cp .env.example .env
   ```

2. **Configurez l'URL de l'API :**
   Dans le fichier `.env`, modifiez l'URL selon votre configuration :
   ```env
   NUXT_PUBLIC_API_BASE=http://localhost:8000/api
   ```

## Démarrage du backend

Pour que le frontend puisse consommer l'API, vous devez démarrer le backend Symfony :

```bash
cd backend
composer install
symfony serve -d
```

L'API sera disponible sur `http://localhost:8000/api`

## Utilisation du store Book

Le store `BookStore` est maintenant disponible et permet de :

- **Récupérer tous les livres :** `bookStore.fetchBooks()`
- **Rechercher des livres :** `bookStore.searchBooks(query)`
- **Récupérer un livre par ID :** `bookStore.fetchBookById(id)`
- **Gestion de la pagination :** `bookStore.fetchBooks(page)`

### Exemple d'utilisation dans un composant :

```vue
<script setup>
const bookStore = useBookStore()

// Charger les livres
onMounted(async () => {
  try {
    await bookStore.fetchBooks()
  } catch (error) {
    console.error('Erreur:', error)
  }
})

// Accéder aux données
const books = computed(() => bookStore.getBooks)
const loading = computed(() => bookStore.isLoading)
</script>
```

## Page de test

Une page de test est disponible sur `/books` qui démontre l'utilisation complète du store Book avec :
- Liste des livres avec pagination
- Recherche par titre
- Affichage des détails d'un livre
- Gestion des erreurs et du chargement

## Configuration CORS

Assurez-vous que le backend autorise les requêtes depuis le frontend. Dans le fichier `.env` du backend, configurez :

```env
CORS_ALLOW_ORIGIN=http://localhost:3000
```

## Types TypeScript

Les types TypeScript sont définis dans le store `BookStore` :
- `Book` : Structure d'un livre
- `Author` : Structure d'un auteur
- `Category` : Structure d'une catégorie
- `BookCollection` : Structure de la collection API Platform
