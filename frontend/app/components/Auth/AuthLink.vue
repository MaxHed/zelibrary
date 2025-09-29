<template>
  <div v-if="!isAuth" class="flex items-center gap-4">
    <NavItem
      to="/login"
      label="Se connecter"
      size="sm"
    />
    <NavItem
      to="/register"
      label="S'inscrire"
      size="xs"
    />
  </div>
  <div v-else class="relative flex items-center gap-2">
    <button @click="toggle" class="flex items-center gap-2 text-white hover:text-gray-200">
      <span>{{ user?.email || 'Mon compte' }}</span>
      <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
      </svg>
    </button>
    <div v-if="open" class="absolute right-0 top-[120%] min-w-[12rem] bg-white rounded-md shadow-lg ring-1 ring-black/5 py-2 z-50">
      <NuxtLink to="/account" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Mon compte</NuxtLink>
      <button @click="handleLogout" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Déconnexion</button>
    </div>
  </div>
</template>

<script setup>
import NavItem from '../UI/NavItem.vue'
import { useAuth } from '@/composable/useAuth'
import { useApi } from '@/composable/useApi'

const { isAuth, user, logout } = useAuth()
const { get } = useApi()
const open = ref(false)
const toggle = () => { open.value = !open.value }
const handleLogout = async () => {
  await logout()
  open.value = false
  await navigateTo('/')
}

onMounted(async () => {
  // Assurer la restauration même si le plugin n'a pas fini
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
      // non connecté côté serveur, ignorer
    }
  }
})
</script>

