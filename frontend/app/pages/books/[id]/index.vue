<template>
	<BookPage :id="route.params.id as string" />
</template>

<script setup lang="ts">
import BookPage from '@/components/BookPage.vue'
const route = useRoute()
</script>