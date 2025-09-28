import { useApi } from '@/composable/useApi'

export function useAuth() {
  const { apiCall } = useApi()
  const isAuth = useState('isAuth', () => false)
  const user = useState<any | null>('user', () => null)

  const restore = () => {
    isAuth.value = localStorage.getItem('isAuth') === '1'
    user.value = JSON.parse(localStorage.getItem('user') || 'null')
  }

  const login = async (email: string, password: string) => {
    await apiCall('POST', '/login', { email, password })
    const me = await apiCall<any>('GET', '/me')
    isAuth.value = true
    user.value = me
    localStorage.setItem('isAuth', '1')
    localStorage.setItem('user', JSON.stringify(me))
  }

  const logout = async () => {
    await apiCall('POST', '/logout')
    isAuth.value = false
    user.value = null
    localStorage.removeItem('isAuth')
    localStorage.removeItem('user')
  }

  return { isAuth, user, restore, login, logout }
}


