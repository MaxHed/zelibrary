// /composables/useErrorApiHandler.js
import { useRouter } from 'vue-router'

export function useErrorApiHandler() {
  const error = ref(null)
  const router = useRouter()

  /**
   * Gère l'erreur provenant d'une requête API.
   * Si l'API retourne un 401, l'utilisateur est redirigé vers /login.
   /**
    * Gère l'erreur provenant d'une requête API.
    * Si l'API retourne un 401, l'utilisateur est redirigé vers /login.
    * @param {any} err - L'objet erreur renvoyé par l'API.
    */
  const handleError = (err: any) => {
    // Réinitialiser l'erreur
    error.value = null

    if (err.response) {
      // Si le code d'erreur est 401, rediriger vers /login
      if (err.response.status === 401) {
        router.push('/login')
        return // On arrête le traitement après la redirection
      }

      const data = err.response.data
      if (data && data.message) {
        error.value = data.message ?? null
      } else {
        error.value = null
      }
    } else if (err.request) {
      error.value = null
    } else {
      error.value = err.message || 'Erreur inconnue.'
    }
  }

  return {
    error,
    handleError
  }
}
