<template>
  <LibraryPage :id="route.params.id as string" />
</template>

<script setup lang="ts">
import LibraryPage from '@/components/LibraryPage.vue'
const route = useRoute()
</script>

<style scoped>
</style>