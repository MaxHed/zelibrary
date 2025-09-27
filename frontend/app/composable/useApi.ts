// /composables/useApi.ts
import { ofetch } from 'ofetch'

export const useApi = () => {
    const config = useRuntimeConfig()
    const token = useState<string | null>('token')  // si vous exposez un token non httpOnly (sinon cookie httpOnly)

    const api = ofetch.create({
        baseURL: <string> config.public.apiBase, // ex: '/api' (proxy) ou une URL externe
        credentials: 'include',
        timeout: 10_000,
        retry: 2,
        onRequest({ options }) {
            if (token.value) {
                // S'assurer que headers est bien un objet, puis ajouter Authorization
                options.headers = Object.assign({}, options.headers || {}, { 'Authorization': `Bearer ${token.value}` });
            }
        },
        onResponseError({ response }) {
            // Centraliser le mapping d'erreurs
            if (response.status === 401) {
                // Rediriger vers la page de connexion
                token.value = null
                navigateTo('/login')
            } else if (response.status === 422) {
                // Afficher les messages de validation
                console.error('Erreur de validation:', response._data)
            } else if (response.status >= 500) {
                // Erreur serveur
                console.error('Erreur serveur:', response._data)
            }
        }
    })

    return api
}
