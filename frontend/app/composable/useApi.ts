type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'

interface ApiOptions {
  params?: Record<string, any>
  headers?: Record<string, string>
  credentials?: RequestCredentials
}

export function useApi() {
  const {
    public: { apiBase },
  } = useRuntimeConfig()

  const apiCall = async <T = any>(method: HttpMethod, endpoint: string, data?: any, options: ApiOptions = {}): Promise<T> => {
    // Éviter les redirections HTTP->HTTPS (307) qui cassent CORS: forcer https en dev si nécessaire
    const base = apiBase.replace(/^http:\/\//, 'https://').replace(/\/$/, '')
    const path = endpoint.startsWith('/') ? endpoint : `/${endpoint}`
    const url = `${base}${path}`
    return await $fetch<T>(url, {
      method,
      body: data,
      query: options.params,
      headers: options.headers,
      credentials: options.credentials ?? 'include',
    })
  }

  return { apiCall }
}


