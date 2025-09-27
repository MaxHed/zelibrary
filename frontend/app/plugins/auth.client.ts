export default defineNuxtPlugin(async () => {
  const authStore = useAuthStore()
  
  // Initialiser l'AuthStore au démarrage
  await authStore.initialize()
})
