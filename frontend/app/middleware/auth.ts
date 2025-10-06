import { useAuth } from "~/composable/useAuth"

/**
 * Middleware d'authentification pour protéger les routes.
 * 
 * Usage dans une page :
 * definePageMeta({
 *   middleware: 'auth'
 * })
 * 
 * Si l'utilisateur n'est pas authentifié, il est redirigé vers /login.
 */
export default defineNuxtRouteMiddleware(async (to, from) => {
  const { isAuth, checkAuth, initialized } = useAuth()

  // Attendre l'initialisation si pas encore faite
  if (!initialized.value) {
    await checkAuth()
  }

  // Si non authentifié, rediriger vers login
  if (!isAuth.value) {
    return navigateTo('/login')
  }
})

