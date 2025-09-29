<template>
  <article class="border rounded p-4 h-full flex flex-col gap-2">
    <header>
      <h3 class="font-semibold text-lg truncate" :title="book.title">{{ book.title }}</h3>
    </header>
    <p v-if="book.summary" class="text-sm opacity-80 line-clamp-3">{{ book.summary }}</p>
    <footer class="mt-auto text-xs opacity-70 flex items-center justify-between">
      <span v-if="authors" :title="authors">{{ authors }}</span>
      <span v-if="categories" :title="categories">{{ categories }}</span>
    </footer>
  </article>
  
</template>

<script setup lang="ts">
interface BookLike {
  id: number | string
  title: string
  summary?: string | null
  authorsNames?: string
  categoriesNames?: string
}

const props = defineProps<{
  book: BookLike
}>()

const authors = computed(() => (props.book as any).authorsNames ?? (props.book as any).authors?.map((a: any) => a.name).join(', '))
const categories = computed(() => (props.book as any).categoriesNames ?? (props.book as any).categories?.map((c: any) => c.name).join(', '))
</script>

<style scoped>
</style>


