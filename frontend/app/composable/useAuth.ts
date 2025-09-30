import { useApi } from './useApi'

export const useAuth = () => {
  const { post, get } = useApi()
  const isAuth = useState('isAuth', () => false)
  const user = useState<any | null>('user', () => null)
  const loading = useState('authLoading', () => false)

  // Restaure l'état client (sans appel réseau), utile au boot côté client
  const restore = () => {
    if (typeof window === 'undefined') return
    isAuth.value = localStorage.getItem('isAuth') === '1'
    user.value = JSON.parse(localStorage.getItem('user') || 'null')
  }

  // Authentifie via /login_check, puis récupère /me (cookie JWT HttpOnly)
  const login = async (email: string, password: string) => {
    loading.value = true
    try {
      const loginResp: any = await post('/login_check', { email, password }, {}, true)
      const me = await get('/me', {}, true) // Utilise le cookie AUTH_TOKEN automatiquement
      isAuth.value = true
      user.value = me
      localStorage.setItem('isAuth', '1')
      localStorage.setItem('user', JSON.stringify(me))
      return { success: true, user: me }
    } catch (e: any) {
      return { success: false, error: e?.data?.message || e?.message || 'Erreur de connexion' }
    } finally {
      loading.value = false
    }
  }

  // Déconnexion côté serveur + nettoyage côté client
  const logout = async () => {
    try {
      await post('/logout', {}, {}, true)
    } finally {
      isAuth.value = false
      user.value = null
      localStorage.removeItem('isAuth')
      localStorage.removeItem('user')
    }
  }

  return { isAuth, user, loading, restore, login, logout }
}


