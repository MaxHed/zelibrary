<template>
  <div class="flex justify-start">
    <NuxtLink to="/books" class="text-sm opacity-70 btn btn-primary">Retour</NuxtLink>
  </div>
  <section class="page-width mx-auto py-6" v-if="book">
    <div class="flex flex-col gap-4">
      <h1 class="text-2xl font-bold">{{ book.title }}</h1>
      <div class="text-sm opacity-70" v-if="book.authorsNames || book.categoriesNames">
        <span v-if="book.authorsNames">Auteurs: {{ book.authorsNames }}</span>
        <span v-if="book.categoriesNames" class="ml-2">Catégories: {{ book.categoriesNames }}</span>
      </div>
      <div class="text-sm opacity-70" v-if="book.averageRate !== undefined">
        Note moyenne: {{ (Number(book.averageRate) || 0).toFixed(1) }} / 5
      </div>
      <p v-if="book.summary" class="text-base">{{ book.summary }}</p>
    </div>
    <!-- reviews -->
    <div class="flex flex-col gap-4 mt-4 border-t pt-4">
      <div class="w-full flex justify-between">
        <h2 class="text-xl font-bold">Reviews</h2>
        <!-- if not logged in show (Vous devez être connecté pour laisser un avis) -->
        <NuxtLink to="/login" v-if="!isAuth" class="text-sm opacity-70 btn btn-primary">Vous devez être connecté pour laisser un avis</NuxtLink>
        <!-- if logged in show (Ajouter un avis) -->
        <NuxtLink :to="`/books/${book.id}/create-review`" v-else class="text-sm opacity-70">Ajouter un avis</NuxtLink>
      </div>
      <div v-if="book.reviews.length > 0" class="flex flex-col gap-2" v-for="review in book.reviews" :key="review.id">
        <div class="flex items-center gap-2">
          <RateStars :rate="review.rate" />
          <p class="text-sm">Note: {{ review.rate }}</p>
        </div>
        <p class="text-sm">{{ review.review }}</p>
        <p class="text-sm">Auteur: {{ review.user.email }}</p>
        <p class="text-sm">Date: {{ new Date(review.createdAt).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
      </div>
      <div v-else class="text-sm">Aucune review trouvée</div>
    </div>
  </section>
  <section v-else class="page-width mx-auto py-6">
    <div class="opacity-70 text-sm">Chargement...</div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useApi } from '@/composable/useApi'
import RateStars from '@/components/UI/RateStars.vue'
import { useAuth } from '@/composable/useAuth'
const { isAuth } = useAuth()

const props = defineProps<{ id: string | number }>()
const { get } = useApi()

const book = ref<any | null>(null)
const error = ref<string | null>(null)

async function fetchBook() {
  try {
    error.value = null
    const res: any = await get(`/books/${props.id}`, {}, true)
    book.value = res
    navigateTo(`/books/${book.value.id}`)
  } catch (e: any) {
    error.value = e?.message ?? 'Erreur de chargement'
  }
}

onMounted(fetchBook)
</script>

<style scoped>
</style>


