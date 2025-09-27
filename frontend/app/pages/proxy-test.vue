<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Test du Proxy</h1>
    
    <div class="space-y-4">
      <button 
        @click="testProxy" 
        :disabled="loading"
        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50"
      >
        {{ loading ? 'Test en cours...' : 'Tester le proxy' }}
      </button>
      
      <div v-if="result" class="mt-4 p-4 bg-green-100 rounded-lg">
        <h3 class="font-bold mb-2">Résultat :</h3>
        <p class="text-sm">✅ Proxy fonctionne ! {{ result.totalItems }} livres trouvés</p>
        <details class="mt-2">
          <summary class="cursor-pointer text-sm text-blue-600">Voir les détails</summary>
          <pre class="text-xs mt-2 overflow-auto bg-white p-2 rounded">{{ JSON.stringify(result, null, 2) }}</pre>
        </details>
      </div>
      
      <div v-if="error" class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg">
        <h3 class="font-bold mb-2">Erreur :</h3>
        <p class="text-sm">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
const loading = ref(false)
const result = ref(null)
const error = ref(null)

const testProxy = async () => {
  loading.value = true
  error.value = null
  result.value = null
  
  try {
    const response = await $fetch('/api/books')
    result.value = response
  } catch (err) {
    error.value = err.message || 'Erreur inconnue'
    console.error('Erreur proxy:', err)
  } finally {
    loading.value = false
  }
}
</script>
