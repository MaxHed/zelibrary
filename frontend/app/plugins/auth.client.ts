import { useAuth } from "~/composable/useAuth"

/**
 * Plugin d'initialisation de l'authentification (client-only).
 * 
 * Vérifie automatiquement l'état d'authentification au boot de l'app
 * en appelant l'API /me avec le cookie JWT HttpOnly.
 * 
 * SÉCURITÉ : Pas de localStorage, l'état est dérivé du cookie serveur.
 */
export default defineNuxtPlugin(async () => {
  const { checkAuth } = useAuth()

  // Vérifier l'authentification au boot (côté client uniquement)
  // Si le cookie AUTH_TOKEN existe, l'utilisateur sera authentifié
  await checkAuth()
})
