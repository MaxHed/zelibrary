<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Bibliothèque de livres</h1>
    
    <!-- Barre de recherche -->
    <div class="mb-6">
      <div class="flex gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Rechercher un livre..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          @keyup.enter="handleSearch"
        />
        <button
          @click="handleSearch"
          :disabled="bookStore.isLoading"
          class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50"
        >
          Rechercher
        </button>
      </div>
    </div>

    <!-- Messages d'erreur -->
    <div v-if="bookStore.getError" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      {{ bookStore.getError }}
      <button @click="bookStore.clearError" class="ml-2 text-red-500 hover:text-red-700">
        ✕
      </button>
    </div>

    <!-- Indicateur de chargement -->
    <div v-if="bookStore.isLoading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
      <p class="mt-2 text-gray-600">Chargement des livres...</p>
    </div>

    <!-- Liste des livres -->
    <div v-else-if="bookStore.getBooks.length > 0" class="space-y-6">
      <div class="flex justify-between items-center">
        <p class="text-gray-600">
          {{ bookStore.totalItems }} livre(s) trouvé(s)
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="book in bookStore.getBooks"
          :key="book.id"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
          @click="selectBook(book)"
        >
          <h3 class="text-xl font-semibold mb-2 line-clamp-2">{{ book.title }}</h3>
          
          <div v-if="book.authors.length > 0" class="mb-2">
            <p class="text-sm text-gray-600">
              <strong>Auteur(s):</strong> {{ book.authors.map(a => a.name).join(', ') }}
            </p>
          </div>

          <div v-if="book.categories.length > 0" class="mb-3">
            <div class="flex flex-wrap gap-1">
              <span
                v-for="category in book.categories"
                :key="category.id"
                class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full"
              >
                {{ category.name }}
              </span>
            </div>
          </div>

          <p v-if="book.summary" class="text-gray-700 text-sm line-clamp-3">
            {{ book.summary }}
          </p>

          <div class="mt-4 flex justify-between items-center text-xs text-gray-500">
            <span>{{ book.mediaType }}</span>
            <span>{{ new Date(book.createdAt).toLocaleDateString('fr-FR') }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Message si aucun livre -->
    <div v-else class="text-center py-12">
      <p class="text-gray-500 text-lg">Aucun livre trouvé</p>
      <button
        @click="loadBooks"
        class="mt-4 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
      >
        Charger les livres
      </button>
    </div>

    <!-- Modal pour afficher les détails d'un livre -->
    <div
      v-if="selectedBook"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
      @click="closeModal"
    >
      <div
        class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <h2 class="text-2xl font-bold">{{ selectedBook.title }}</h2>
            <button
              @click="closeModal"
              class="text-gray-500 hover:text-gray-700 text-2xl"
            >
              ✕
            </button>
          </div>

          <div v-if="selectedBook.authors.length > 0" class="mb-4">
            <h3 class="font-semibold mb-2">Auteur(s):</h3>
            <p>{{ selectedBook.authors.map(a => a.name).join(', ') }}</p>
          </div>

          <div v-if="selectedBook.categories.length > 0" class="mb-4">
            <h3 class="font-semibold mb-2">Catégories:</h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="category in selectedBook.categories"
                :key="category.id"
                class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full"
              >
                {{ category.name }}
              </span>
            </div>
          </div>

          <div v-if="selectedBook.summary" class="mb-4">
            <h3 class="font-semibold mb-2">Résumé:</h3>
            <p class="text-gray-700">{{ selectedBook.summary }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
            <div>
              <strong>Type de média:</strong> {{ selectedBook.mediaType }}
            </div>
            <div>
              <strong>Date de création:</strong> {{ new Date(selectedBook.createdAt).toLocaleDateString('fr-FR') }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const bookStore = useBookStore()
const searchQuery = ref('')
const selectedBook = ref<Book | null>(null)

// Charger les livres au montage du composant
onMounted(() => {
  loadBooks()
})

const loadBooks = async () => {
  try {
    await bookStore.fetchBooks()
  } catch (error) {
    console.error('Erreur lors du chargement des livres:', error)
  }
}

const handleSearch = async () => {
  try {
    if (searchQuery.value.trim()) {
      await bookStore.searchBooks(searchQuery.value.trim())
    } else {
      await bookStore.fetchBooks()
    }
  } catch (error) {
    console.error('Erreur lors de la recherche:', error)
  }
}

const selectBook = (book: Book) => {
  selectedBook.value = book
}

const closeModal = () => {
  selectedBook.value = null
}

// Plus besoin de pagination - tous les livres sont chargés d'un coup
const nextPage = () => {
  // Fonction désactivée - tous les livres sont affichés
}

const previousPage = () => {
  // Fonction désactivée - tous les livres sont affichés
}

// Nettoyer les données au démontage
onUnmounted(() => {
  bookStore.clearBooks()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
