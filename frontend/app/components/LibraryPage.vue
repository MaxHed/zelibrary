<template>
  <section class="page-width mx-auto py-6" v-if="library">
    <div class="flex flex-col gap-4">
      <h1 class="text-2xl font-bold">{{ library.name }}</h1>
      <div class="text-sm opacity-70 flex flex-wrap gap-3">
        <span v-if="library.address">{{ library.address }}</span>
        <span v-if="library.phone">{{ library.phone }}</span>
        <a v-if="library.website" :href="library.website" target="_blank" rel="noopener" class="underline">Site</a>
      </div>
      <p v-if="library.description" class="text-base">{{ library.description }}</p>
      <div class="text-sm opacity-70" v-if="library.booksCount !== undefined">{{ library.booksCount }} livres</div>
    </div>
  </section>
  <section v-else class="page-width mx-auto py-6">
    <div class="opacity-70 text-sm">Chargement...</div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useApi } from '@/composable/useApi'

const props = defineProps<{ id: string | number }>()
const { get } = useApi()

const library = ref<any | null>(null)
const error = ref<string | null>(null)

async function fetchLibrary() {
  try {
    error.value = null
    const res: any = await get(`/libraries/${props.id}`, {}, true)
    library.value = res
  } catch (e: any) {
    error.value = e?.message ?? 'Erreur de chargement'
  }
}

onMounted(fetchLibrary)
</script>

<style scoped>
</style>


