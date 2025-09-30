// Client HTTP minimal basé sur $fetch, avec gestion d'erreur centralisée
import { useErrorApiHandler } from './useErrorApiHandler'

const joinURL = (base: string, path: string) => {
  const b = base.replace(/\/+$/, '')
  const p = path.replace(/^\/+/, '')
  return `${b}/${p}`
}

export function useApi() {
  const { error, handleError } = useErrorApiHandler()

  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase

  const callApi = async (
    url: string,
    options: any = {},
    withCredentials = false
  ) => {
    try {
      const mergedOptions = {
        ...options,
        credentials: withCredentials ? 'include' : 'omit',
      }

      const fullUrl = joinURL(baseURL, url)
      const response = await $fetch(fullUrl, mergedOptions)
      return response
    } catch (err) {
      handleError(err)
      throw err
    }
  }

  // Helpers HTTP
  const get = (url: string, opts: any = {}, withCredentials = false) =>
    callApi(url, { method: 'GET', ...opts }, withCredentials)

  const post = (url: string, payload?: any, opts: any = {}, withCredentials = false) =>
    callApi(url, { method: 'POST', body: payload, ...opts }, withCredentials)

  const put = (url: string, payload?: any, opts: any = {}, withCredentials = false) =>
    callApi(url, { method: 'PUT', body: payload, ...opts }, withCredentials)

  const remove = (url: string, opts: any = {}, withCredentials = false) =>
    callApi(url, { method: 'DELETE', ...opts }, withCredentials)

  return {
    error,
    callApi,
    get,
    post,
    put,
    remove,
  }
}
