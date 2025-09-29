<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Bibliothèque de livres</h1>
    
    <!-- Indicateur de chargement -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
      <p class="mt-2 text-gray-600">Chargement des livres...</p>
    </div>

    <!-- Liste des livres -->
    <div v-else-if="books.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="book in books"
        :key="book.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
      >
        <h3 class="text-xl font-semibold mb-2">{{ book.title }}</h3>
      </div>
    </div>

    <!-- Message si aucun livre -->
    <div v-else class="text-center py-12">
      <p class="text-gray-500 text-lg">Aucun livre trouvé</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useBooks } from '@/composable/useBooks'

const { fetchBooks, books, loading } = useBooks()

// Charger les livres au montage du composant
onMounted(() => {
  fetchBooks()
})
</script>


