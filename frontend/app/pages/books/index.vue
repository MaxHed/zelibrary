<template>
	<ResourceCollection
		resource-path="/books"
		:search-props="['title', 'authors.name', 'categories.name']"
	>
		<template #item="{ item }">
			<NuxtLink :to="`/books/${item.id}`">
				<div class="flex flex-col gap-2">
					<h3 class="font-semibold">{{ item.title }}</h3>
					<div class="text-xs opacity-70" v-if="item.authorsNames || item.categoriesNames">
						<span v-if="item.authorsNames">Auteurs: {{ item.authorsNames }}</span>
						<span v-if="item.categoriesNames" class="ml-2">Catégories: {{ item.categoriesNames }}</span>
					</div>
					<!-- <p v-if="item.summary" class="text-sm opacity-80 line-clamp-3">{{ item.summary }}</p> -->
					<div class="text-xs opacity-70" v-if="item.averageRate !== undefined">
						Note moyenne: {{ (Number(item.averageRate) || 0).toFixed(1) }} / 5
					</div>
				</div>
			</NuxtLink>
		</template>
	</ResourceCollection>
</template>

<script setup lang="ts">
import ResourceCollection from '@/components/ResourceCollection.vue'
</script>
