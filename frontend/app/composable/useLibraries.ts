import { useApi } from '@/composable/useApi'

interface Library { id: number; name: string }

export function useLibraries() {
  const { apiCall } = useApi()
  const libraries = ref<Library[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchLibraries = async () => {
    try {
      loading.value = true
      error.value = null
      const res: any = await apiCall('GET', '/libraries/')
      libraries.value = res['hydra:member'] ?? res ?? []
    } catch (e: any) {
      error.value = e?.message ?? 'Erreur de chargement'
    } finally {
      loading.value = false
    }
  }

  return { libraries, loading, error, fetchLibraries }
}


