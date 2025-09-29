<template>
	<section class="page-width mx-auto py-8 max-w-3xl">
		<h1 class="text-2xl font-bold mb-6">Rédiger une review</h1>

		<form class="flex flex-col gap-5" @submit.prevent="onSubmit">
			<div>
				<label class="block text-sm font-medium mb-2">Note</label>
				<div class="flex items-center gap-4">
					<select v-model.number="rate" class="border rounded px-3 py-2" :disabled="loading">
						<option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
					</select>
					<RateStars :rate="rate" size="lg" />
				</div>
			</div>

			<div>
				<label class="block text-sm font-medium mb-2">Votre avis</label>
				<textarea
					v-model.trim="review"
					rows="6"
					class="w-full border rounded px-3 py-2"
					placeholder="Partagez votre expérience..."
					:disabled="loading"
					required
				/>
			</div>

			<div class="flex items-center gap-3">
				<button type="submit" class="px-4 py-2 bg-[var(--color-primary)] text-white rounded" :disabled="loading">
					{{ loading ? 'Envoi...' : 'Publier la review' }}
				</button>
				<NuxtLink :to="`/books/${bookId}`" class="px-4 py-2 border rounded">Annuler</NuxtLink>
			</div>

			<p v-if="error" class="text-sm text-red-600">{{ error }}</p>
			<p v-if="success" class="text-sm text-green-600">Review publiée avec succès.</p>
		</form>
	</section>
</template>

<script setup lang="ts">
import RateStars from '@/components/UI/RateStars.vue'
import { useApi } from '@/composable/useApi'

const route = useRoute()
const bookId = route.params.id as string

const { post } = useApi()

const rate = ref<number>(5)
const review = ref<string>('')
const loading = ref<boolean>(false)
const error = ref<string | null>(null)
const success = ref<boolean>(false)

const onSubmit = async () => {
	try {
		loading.value = true
		error.value = null
		success.value = false
		await post(`/reviews/books/${bookId}`, { rate: rate.value, review: review.value }, {}, true)
		success.value = true
		// Optionnel: rediriger vers la page du livre après succès
		await navigateTo(`/books/${bookId}`)
	} catch (e: any) {
		error.value = e?.data?.message || e?.message || 'Impossible de publier les avis'
	} finally {
		loading.value = false
	}
}
</script>

<style scoped>
</style>
