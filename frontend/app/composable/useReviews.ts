import { useApi } from '@/composable/useApi'

interface Review { id: number; rate: number; review?: string }

export function useReviews(bookId: number) {
  const { get, post } = useApi()
  const reviews = ref<Review[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchReviews = async () => {
    try {
      loading.value = true
      error.value = null
      const res: any = await get(`/books/${bookId}/reviews/`, {}, true)
      reviews.value = res['hydra:member'] ?? []
    } catch (e: any) {
      error.value = e?.message ?? 'Erreur de chargement'
    } finally {
      loading.value = false
    }
  }

  const createReview = async (payload: { rate: number; review?: string }) => {
    await post(`/books/${bookId}/reviews`, payload, {}, true)
    await fetchReviews()
  }

  return { reviews, loading, error, fetchReviews, createReview }
}


