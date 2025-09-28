import { useApi } from '@/composable/useApi'

interface Author { id: number; name: string }

export function useAuthors() {
  const { get } = useApi()
  const authors = ref<Author[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchAuthors = async () => {
    try {
      loading.value = true
      error.value = null
      const res: any = await get('/authors/', {}, true)
      authors.value = res['hydra:member'] ?? []
    } catch (e: any) {
      error.value = e?.message ?? 'Erreur de chargement'
    } finally {
      loading.value = false
    }
  }

  return { authors, loading, error, fetchAuthors }
}


