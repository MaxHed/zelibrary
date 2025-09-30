// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  // Modules Nuxt & écosystème
  modules: ['@nuxtjs/tailwindcss', '@nuxt/image', '@pinia/nuxt', 'pinia-plugin-persistedstate/nuxt'],
  css: ['~/assets/css/main.css'],
  imports: {
    // Active l'auto-import pour les composables dans app/composable/**
    dirs: ['app/composable/**']
  },
  runtimeConfig: {
    public: {
      // Base API côté client (Reverse-proxy Nitro en dev)
      apiBase: '/api'
    }
  },
  nitro: {
    devProxy: {
      // Proxy dev vers le backend Symfony
      '/api/': { target: 'http://localhost:8000', changeOrigin: true }
    }
  },
})