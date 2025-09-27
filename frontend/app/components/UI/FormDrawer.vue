<template>
  <Drawer
    :is-open="isOpen"
    :title="title"
    :show-header="true"
    :show-footer="true"
    :size="size"
    position="right"
    @close="$emit('close')"
    :background-color="backgroundColor"
    :text-color="textColor"
  >
    <!-- Form Content -->
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <slot />
    </form>

    <!-- Footer with action buttons -->
    <template #footer>
      <div class="flex justify-end space-x-3">
        <button
          type="button"
          @click="$emit('close')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Annuler
        </button>
        <button
          type="submit"
          @click="handleSubmit"
          :disabled="isLoading"
          class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="isLoading" class="flex items-center">
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            En cours...
          </span>
          <span v-else>{{ submitText }}</span>
        </button>
      </div>
    </template>
  </Drawer>
</template>

<script setup>
import Drawer from './Drawer.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Formulaire'
  },
  size: {
    type: String,
    default: 'md'
  },
  submitText: {
    type: String,
    default: 'Enregistrer'
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'submit'])

const handleSubmit = () => {
  emit('submit')
}
</script>
