import { useApi } from './useApi'

/**
 * Composable d'authentification sécurisé.
 * 
 * SÉCURITÉ : N'utilise PLUS localStorage (vulnérable XSS).
 * L'état d'auth est dérivé du cookie JWT HttpOnly côté serveur.
 * 
 * Le cookie AUTH_TOKEN est automatiquement envoyé avec credentials: 'include'.
 */
export const useAuth = () => {
  const { post, get } = useApi()
  const isAuth = useState('isAuth', () => false)
  const user = useState<any | null>('user', () => null)
  const loading = useState('authLoading', () => false)
  const initialized = useState('authInitialized', () => false)

  /**
   * Vérifie l'état d'authentification en appelant /me.
   * Retourne true si authentifié, false sinon.
   * Appelé automatiquement au boot de l'app (voir plugins/auth.client.ts)
   */
  const checkAuth = async (): Promise<boolean> => {
    if (initialized.value) return isAuth.value

    loading.value = true
    try {
      const me = await get('/me', {}, true)
      isAuth.value = true
      user.value = me
      initialized.value = true
      return true
    } catch (e: any) {
      // 401 ou autre erreur = non authentifié
      isAuth.value = false
      user.value = null
      initialized.value = true
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Authentifie via /login_check, puis récupère /me.
   * Le token JWT est stocké dans un cookie HttpOnly côté serveur.
   */
  const login = async (email: string, password: string) => {
    loading.value = true
    try {
      await post('/login_check', { email, password }, {}, true)
      const me = await get('/me', {}, true)
      isAuth.value = true
      user.value = me
      initialized.value = true
      return { success: true, user: me }
    } catch (e: any) {
      isAuth.value = false
      user.value = null
      return { success: false, error: e?.data?.message || e?.message || 'Erreur de connexion' }
    } finally {
      loading.value = false
    }
  }

  /**
   * Déconnexion côté serveur (supprime le cookie JWT).
   */
  const logout = async () => {
    try {
      await post('/logout', {}, {}, true)
    } finally {
      isAuth.value = false
      user.value = null
      initialized.value = false
    }
  }

  return { isAuth, user, loading, initialized, checkAuth, login, logout }
}


