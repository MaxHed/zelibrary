<template>
  <Drawer
    :is-open="isOpen"
    :title="title"
    :show-header="showHeader"
    :show-footer="showFooter"
    size="sm"
    position="right"
    @close="$emit('close')"
    :background-color="backgroundColor"
    :text-color="textColor"
  >
    <!-- Navigation Content -->
    <div class="space-y-1">
      <slot name="navigation">
        <div class="flex justify-center items-center">
        <AuthLink />
        </div>
        <NavItem
          v-for="item in navigationItems"
          :key="item.to"
          :to="item.to"
          :label="item.label"
          class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md"
        />
      </slot>
    </div>

    <!-- Footer slot for additional actions -->
    <template #footer>
      <slot name="footer" />
    </template>
  </Drawer>
</template>

<script setup>
import Drawer from './Drawer.vue'
import NavItem from './NavItem.vue'
import AuthLink from '../Auth/AuthLink.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Menu'
  },
  showHeader: {
    type: Boolean,
    default: true
  },
  showFooter: {
    type: Boolean,
    default: false
  },
  navigationItems: {
    type: Array,
    default: () => [
      { to: '/', label: 'Accueil' },
      { to: '/about', label: 'À propos' },
      { to: '/contact', label: 'Contact' }
    ]
  },
  backgroundColor: {
    type: String,
    default: 'white'
  },
  textColor: {
    type: String,
    default: 'black'
  }
})

defineEmits(['close'])
</script>
