import { useApi } from "~/composable/useApi"
import { useAuth } from "~/composable/useAuth"

export default defineNuxtPlugin(async (nuxtApp) => {
  if (import.meta.env.SSR) return

  const { restore, isAuth, user } = useAuth()
  const { get } = useApi()

  // Restaurer depuis localStorage (isAuth + user)
  restore()

  // Si non authentifié mais cookie JWT présent, tenter /me pour rétablir la session
  if (!isAuth.value) {
    try {
      const me = await get('/me', {}, true)
      if (me) {
        isAuth.value = true
        user.value = me
        localStorage.setItem('isAuth', '1')
        localStorage.setItem('user', JSON.stringify(me))
      }
    } catch {
      // Ignorer: pas connecté côté serveur
    }
  }
})


