import { defineStore } from 'pinia'

// Types basés sur l'entité Book du backend
export interface Author {
  id: number
  name: string
  birthYear?: number
  deathYear?: number
}

export interface Category {
  id: number
  name: string
}

export interface Book {
  id: number
  title: string
  summary?: string
  mediaType: string
  formats?: Record<string, string>
  authors: Author[]
  categories: Category[]
  createdAt: string
  updatedAt: string
}

export interface BookCollection {
  '@context': string
  '@id': string
  '@type': string
  member: Book[]
  totalItems: number
}

export const useBookStore = defineStore('book', () => {
  // État
  const books = ref<Book[]>([])
  const currentBook = ref<Book | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const totalItems = ref(0)
  const currentPage = ref(1)
  const itemsPerPage = ref(20)

  // Getters
  const getBooks = computed(() => books.value)
  const getCurrentBook = computed(() => currentBook.value)
  const isLoading = computed(() => loading.value)
  const getError = computed(() => error.value)
  const getTotalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value))

  // Actions
  const fetchBooks = async (search?: string) => {
    try {
      loading.value = true
      error.value = null
      
      const config = useRuntimeConfig()
      const token = useState<string | null>('token')
      
      // Utiliser directement l'URL HTTPS
      const response = await $fetch<BookCollection>('https://127.0.0.1:8000/api/books', {
        headers: {
          'Content-Type': 'application/json',
          ...(token.value && { 'Authorization': `Bearer ${token.value}` })
        }
      })

      // Gérer les deux structures possibles de l'API sans erreur de typage
      if ('member' in response && 'totalItems' in response) {
        books.value = response.member
        totalItems.value = response.totalItems
      } else if ('hydra:member' in response && 'hydra:totalItems' in response) {
        // @ts-ignore
        books.value = response['hydra:member']
        // @ts-ignore
        totalItems.value = response['hydra:totalItems']
      } else {
        books.value = []
        totalItems.value = 0
      }

      return response
    } catch (err: any) {
      error.value = err.message || 'Erreur lors de la récupération des livres'
      console.error('Erreur fetchBooks:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchBookById = async (id: number) => {
    try {
      loading.value = true
      error.value = null
      
      const config = useRuntimeConfig()
      const token = useState<string | null>('token')
      
      const response = await $fetch<Book>(`https://127.0.0.1:8000/api/books/${id}`, {
        headers: {
          'Content-Type': 'application/json',
          ...(token.value && { 'Authorization': `Bearer ${token.value}` })
        }
      })
      
      currentBook.value = response
      return response
    } catch (err: any) {
      error.value = err.message || 'Erreur lors de la récupération du livre'
      console.error('Erreur fetchBookById:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const searchBooks = async (query: string) => {
    return await fetchBooks(query)
  }

  const clearBooks = () => {
    books.value = []
    currentBook.value = null
    error.value = null
    totalItems.value = 0
    currentPage.value = 1
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // État
    books,
    currentBook,
    loading,
    error,
    totalItems,
    currentPage,
    itemsPerPage,
    
    // Getters
    getBooks,
    getCurrentBook,
    isLoading,
    getError,
    getTotalPages,
    
    // Actions
    fetchBooks,
    fetchBookById,
    searchBooks,
    clearBooks,
    clearError
  }
}, {
  persist: true
})
