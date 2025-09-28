import { useApi } from '@/composable/useApi'

interface Category { id: number; name: string }

export function useCategories() {
  const { get } = useApi()
  const categories = ref<Category[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchCategories = async () => {
    try {
      loading.value = true
      error.value = null
      const res: any = await get('/categories/', {}, true)
      categories.value = res['hydra:member'] ?? []
    } catch (e: any) {
      error.value = e?.message ?? 'Erreur de chargement'
    } finally {
      loading.value = false
    }
  }

  return { categories, loading, error, fetchCategories }
}


