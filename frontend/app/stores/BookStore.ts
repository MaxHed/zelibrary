import { defineStore } from 'pinia'
import { useApi } from '~/composable/useApi'

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
  'hydra:member': Book[]
  'hydra:totalItems': number
  'hydra:view'?: {
    '@id': string
    '@type': string
    'hydra:first'?: string
    'hydra:last'?: string
    'hydra:next'?: string
    'hydra:previous'?: string
  }
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
  const fetchBooks = async (page: number = 1, search?: string) => {
    try {
      loading.value = true
      error.value = null
      
      const api = useApi()
      const params: Record<string, any> = {
        page: page - 1, // API Platform utilise une pagination basée sur 0
        'itemsPerPage': itemsPerPage.value
      }
      
      if (search) {
        params.title = search
      }

      const response = await api<BookCollection>('/books', {
        query: params
      })

      books.value = response['hydra:member']
      totalItems.value = response['hydra:totalItems']
      currentPage.value = page

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
      
      const api = useApi()
      const response = await api<Book>(`/books/${id}`)
      
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

  const searchBooks = async (query: string, page: number = 1) => {
    return await fetchBooks(page, query)
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
