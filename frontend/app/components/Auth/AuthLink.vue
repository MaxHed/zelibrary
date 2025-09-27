<template>
  <div v-if="!authStore.isLoggedIn" class="flex items-center gap-4">
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
  <div v-else class="flex items-center gap-4">
    <span class="text-white">
      Bonjour,
      {{
        authStore.user?.firstName || authStore.user?.email || "Utilisateur"
      }}
      !
    </span>
    <button
      @click="handleLogout"
      class="text-white hover:text-gray-200 transition-colors"
      :disabled="authStore.isLoading"
    >
      {{ authStore.isLoading ? "Déconnexion..." : "Se déconnecter" }}
    </button>
  </div>
</template>

<script setup>
import NavItem from '../UI/NavItem.vue';

const authStore = useAuthStore()

const handleLogout = async () => {
    await authStore.logout()
}
</script>

