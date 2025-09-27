<template>
  <div>
    <!-- Overlay -->
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        @click="closeDrawer"
      />
    </Transition>

    <!-- Drawer -->
    <Transition
      enter-active-class="transition-transform duration-300 ease-out"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-200 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div
        v-if="isOpen"
        class="fixed inset-y-0 right-0 z-50 w-full max-w-sm bg-white shadow-xl"
        :style="{ backgroundColor: backgroundColor, color: textColor }"
        :class="drawerClasses"
      >
        <!-- Header -->
        <div v-if="showHeader" class="flex items-center justify-between px-4 py-6 border-b border-gray-200">
          <h2 v-if="title" class="text-lg font-semibold" :style="{ color: textColor }">{{ title }}</h2>
          <button
            @click="closeDrawer"
            class="text-gray-400 hover:text-gray-600 transition-colors"
            :style="{ color: textColor }"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto" :class="contentClasses">
          <slot />
        </div>

        <!-- Footer -->
        <div v-if="showFooter" class="px-4 py-6 border-t border-gray-200">
          <slot name="footer" />
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  showHeader: {
    type: Boolean,
    default: true
  },
  showFooter: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'sm', // sm, md, lg, xl, full
    validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value)
  },
  position: {
    type: String,
    default: 'right', // right, left
    validator: (value) => ['right', 'left'].includes(value)
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

const emit = defineEmits(['close'])

const closeDrawer = () => {
  emit('close')
}

const drawerClasses = computed(() => {
  const sizeClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    full: 'max-w-full'
  }
  
  const positionClasses = {
    right: 'right-0',
    left: 'left-0'
  }
  
  return [
    sizeClasses[props.size],
    positionClasses[props.position]
  ]
})

const contentClasses = computed(() => {
  return props.showHeader ? 'px-4 py-6' : 'p-4'
})
</script>
