// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  modules: ['@nuxtjs/tailwindcss', '@nuxt/image', '@pinia/nuxt', 'pinia-plugin-persistedstate/nuxt'],
  css: ['~/assets/css/main.css'],
  imports: {
    // Active l'auto-import pour les composables dans app/composable/**
    dirs: ['app/composable/**']
  },
  runtimeConfig: {
    public: {
      // URL backend en dev HTTP (aligné sur localhost pour que SameSite=Lax fonctionne)
      apiBase: '/api'
    }
  },
  nitro: {
    devProxy: {
      '/api/': { target: 'http://localhost:8000', changeOrigin: true }
    }
  },
})