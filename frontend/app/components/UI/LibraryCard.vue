<template>
  <article class="border rounded p-4 h-full flex flex-col gap-2">
    <header>
      <h3 class="font-semibold text-lg truncate" :title="library.name">{{ library.name }}</h3>
    </header>
    <p v-if="library.description" class="text-sm opacity-80 line-clamp-3">{{ library.description }}</p>
    <footer class="mt-auto text-xs opacity-70 flex items-center justify-between">
      <span v-if="booksCount !== null">{{ booksCount }} livres</span>
    </footer>
  </article>
  
</template>

<script setup lang="ts">
interface LibraryLike {
  id: number | string
  name: string
  description?: string | null
  booksCollection?: any[]
  booksCount?: number
}

const props = defineProps<{
  library: LibraryLike
}>()

const booksCount = computed<number | null>(() => {
  const l: any = props.library
  if (typeof l.booksCount === 'number') return l.booksCount
  if (Array.isArray(l.booksCollection)) return l.booksCollection.length
  return null
})
</script>

<style scoped>
</style>


