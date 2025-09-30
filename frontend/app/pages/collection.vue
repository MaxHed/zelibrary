<template>
  <ResourceCollection
		ref="collectionRef"
		resource-path="/me/books-collection"
		:search-props="['title', 'authors.name', 'categories.name']"
		:with-credentials="true"
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
			<!-- Bouton hors du lien pour éviter la navigation -->
			<button class="bg-red-500 text-white px-2 py-1 rounded mt-2" @click.stop.prevent="openConfirmationPopup(item.id)">
				Retirer de la collection
			</button>
		</template>
	</ResourceCollection>

	<!-- Modal de confirmation global -->
	<div v-if="confirmationPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" @click.self="closeConfirmationPopup">
		<div class="bg-white p-4 rounded max-w-sm w-full">
			<p class="mb-4">Voulez-vous vraiment retirer ce livre de votre collection ?</p>
			<div class="flex justify-end gap-2">
				<button class="bg-gray-500 text-white px-3 py-1 rounded" @click="closeConfirmationPopup" :disabled="deleting">Annuler</button>
				<button class="bg-red-600 text-white px-3 py-1 rounded" @click="confirmRemove" :disabled="deleting">
					<span v-if="!deleting">Retirer</span>
					<span v-else>Suppression...</span>
				</button>
			</div>
		</div>
	</div>
</template>

<script setup>
import ResourceCollection from '@/components/ResourceCollection.vue'
import { useApi } from "@/composable/useApi";

const collectionRef = ref(null)
const confirmationPopup = ref(false)
const bookId = ref(null)
const deleting = ref(false)
const { remove } = useApi()

const confirmRemove = async () => {
    if (!bookId.value) return
    try {
        deleting.value = true
        await remove(`/me/delete-book-from-my-collection/${bookId.value}`, {}, true)
		
        confirmationPopup.value = false
        bookId.value = null
        await collectionRef.value?.fetchCollection()
    } catch (error) {
        console.error(error)
    } finally {
        deleting.value = false
    }
}

const openConfirmationPopup = (id) => {
	confirmationPopup.value = true
	bookId.value = id
}

const closeConfirmationPopup = () => {
	confirmationPopup.value = false
    bookId.value = null
}
</script>

<style scoped>

</style>
