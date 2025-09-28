<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Démonstration des Drawers</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Drawer de base -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">Drawer de base</h2>
          <p class="text-gray-600 mb-4">Un drawer simple avec overlay et animations fluides.</p>
          <button
            @click="openBasicDrawer"
            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
          >
            Ouvrir le drawer
          </button>
        </div>

        <!-- Drawer de formulaire -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">Drawer de formulaire</h2>
          <p class="text-gray-600 mb-4">Parfait pour les formulaires avec boutons d'action.</p>
          <button
            @click="openFormDrawer"
            class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors"
          >
            Ouvrir le formulaire
          </button>
        </div>

        <!-- Drawer large -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">Drawer large</h2>
          <p class="text-gray-600 mb-4">Un drawer plus large pour plus de contenu.</p>
          <button
            @click="openLargeDrawer"
            class="w-full bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors"
          >
            Ouvrir le drawer large
          </button>
        </div>
      </div>

      <!-- Drawers -->
      <Drawer
        :is-open="basicDrawerOpen"
        title="Drawer de base"
        @close="basicDrawerOpen = false"
      >
        <div class="space-y-4">
          <p class="text-gray-600">Ceci est un exemple de contenu dans un drawer de base.</p>
          <div class="bg-gray-100 p-4 rounded-md">
            <h3 class="font-semibold mb-2">Fonctionnalités :</h3>
            <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
              <li>Overlay avec animation</li>
              <li>Fermeture en cliquant sur l'overlay</li>
              <li>Bouton de fermeture dans le header</li>
              <li>Animations fluides</li>
            </ul>
          </div>
        </div>
      </Drawer>

      <FormDrawer
        :is-open="formDrawerOpen"
        title="Nouveau projet"
        submit-text="Créer le projet"
        :is-loading="isSubmitting"
        @close="formDrawerOpen = false"
        @submit="handleFormSubmit"
      >
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du projet</label>
            <input
              v-model="formData.name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Entrez le nom du projet"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              v-model="formData.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="Décrivez votre projet"
            ></textarea>
          </div>
        </div>
      </FormDrawer>

      <Drawer
        :is-open="largeDrawerOpen"
        title="Drawer large"
        size="lg"
        @close="largeDrawerOpen = false"
      >
        <div class="space-y-6">
          <p class="text-gray-600">Ce drawer utilise une taille large pour afficher plus de contenu.</p>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 p-4 rounded-md">
              <h3 class="font-semibold text-blue-900 mb-2">Section 1</h3>
              <p class="text-blue-700 text-sm">Contenu de la première section avec un fond bleu.</p>
            </div>
            <div class="bg-green-50 p-4 rounded-md">
              <h3 class="font-semibold text-green-900 mb-2">Section 2</h3>
              <p class="text-green-700 text-sm">Contenu de la deuxième section avec un fond vert.</p>
            </div>
          </div>

          <div class="bg-yellow-50 p-4 rounded-md">
            <h3 class="font-semibold text-yellow-900 mb-2">Section complète</h3>
            <p class="text-yellow-700 text-sm">Cette section s'étend sur toute la largeur du drawer.</p>
          </div>
        </div>
      </Drawer>
    </div>
  </div>
</template>

<script setup>
import Drawer from '~/components/UI/Drawer.vue'
import FormDrawer from '~/components/UI/FormDrawer.vue'

// État des drawers
const basicDrawerOpen = ref(false)
const formDrawerOpen = ref(false)
const largeDrawerOpen = ref(false)
const isSubmitting = ref(false)

// Données du formulaire
const formData = ref({
  name: '',
  description: ''
})

// Fonctions pour ouvrir les drawers
const openBasicDrawer = () => {
  basicDrawerOpen.value = true
}

const openFormDrawer = () => {
  formDrawerOpen.value = true
}

const openLargeDrawer = () => {
  largeDrawerOpen.value = true
}

// Gestion de la soumission du formulaire
const handleFormSubmit = async () => {
  isSubmitting.value = true
  
  // Simulation d'une requête API
  await new Promise(resolve => setTimeout(resolve, 2000))
  
  console.log('Données du formulaire:', formData.value)
  
  isSubmitting.value = false
  formDrawerOpen.value = false
  
  // Reset du formulaire
  formData.value = {
    name: '',
    description: ''
  }
}
</script>
