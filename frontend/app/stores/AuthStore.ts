import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: null as string | null,
    user: null as any,
    isLoading: false,
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token,
    isLoggedIn: (state) => !!state.token && !!state.user,
  },
  
  actions: {
    setToken(token: string | null) {
      this.token = token
    },
    
    setUser(user: any) {
      this.user = user
    },
    
    setLoading(loading: boolean) {
      this.isLoading = loading
    },
    
    async login(email: string, password: string) {
      this.setLoading(true)
      try {
        const { $fetch } = useNuxtApp()
        const config = useRuntimeConfig()
        
        const response = await ($fetch as any)('/api/login', {
          method: 'POST',
          baseURL: config.public.apiBase,
          body: {
            email,
            password
          }
        })
        
        if (response.token) {
          this.setToken(response.token)
          this.setUser(response.user)
          
          // Mettre à jour le token dans useApi
          const token = useState<string | null>('token')
          token.value = response.token
          
          return { success: true, user: response.user }
        }
        
        throw new Error('Token non reçu')
      } catch (error: any) {
        console.error('Erreur de connexion:', error)
        return { 
          success: false, 
          error: error.data?.message || 'Erreur de connexion' 
        }
      } finally {
        this.setLoading(false)
      }
    },
    
    async logout() {
      try {
        const { $fetch } = useNuxtApp()
        const config = useRuntimeConfig()
        
        // Appeler l'endpoint de déconnexion côté serveur
        await ($fetch as any)('/api/logout', {
          method: 'POST',
          baseURL: config.public.apiBase,
          headers: this.token ? { 'Authorization': `Bearer ${this.token}` } : {}
        })
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error)
      } finally {
        // Nettoyer l'état local
        this.setToken(null)
        this.setUser(null)
        
        // Nettoyer le token dans useApi
        const token = useState<string | null>('token')
        token.value = null
        
        // Rediriger vers la page d'accueil
        await navigateTo('/')
      }
    },
    
    async fetchUser() {
      if (!this.token) return null
      
      this.setLoading(true)
      try {
        const { $fetch } = useNuxtApp()
        const config = useRuntimeConfig()
        
        const user = await ($fetch as any)('/api/me', {
          baseURL: config.public.apiBase,
          headers: {
            'Authorization': `Bearer ${this.token}`
          }
        })
        
        this.setUser(user)
        return user
      } catch (error) {
        console.error('Erreur lors de la récupération des infos utilisateur:', error)
        // Si l'erreur est 401, déconnecter l'utilisateur
        if ((error as any).status === 401) {
          this.logout()
        }
        return null
      } finally {
        this.setLoading(false)
      }
    },
    
    async initialize() {
      // Vérifier s'il y a un token en localStorage
      if (this.token) {
        // Essayer de récupérer les infos utilisateur
        await this.fetchUser()
      }
    }
  },

  persist: true,
})