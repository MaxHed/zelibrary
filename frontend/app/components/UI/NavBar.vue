<template>
    <div class="page-width">
        <div class="flex justify-end items-center">
            <div class="md:flex hidden items-center gap-4 ">
                <NavItem v-for="item in items" :key="item.to" :to="item.to" :label="item.label" />
            </div>
            <div class="md:hidden flex items-center gap-4">
                <button class="text-white hover:text-gray-200 transition-colors" @click="toggleMenu">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Drawer -->
        <MobileDrawer
            :is-open="isMenuOpen"
            title="Menu"
            :navigation-items="items"
            @close="toggleMenu"
            background-color="var(--color-primary)"
            text-color="var(--color-secondary)"
        />
    </div>
</template>

<script setup>
import NavItem from './NavItem.vue';
import MobileDrawer from './MobileDrawer.vue';
import { useAuth } from '@/composable/useAuth'

const { isAuth } = useAuth()

const baseItems = [
    { to: '/', label: 'Accueil' },
    { to: '/libraries', label: 'Bibliothèques' },
    { to: '/books', label: 'Livres' },
]

// si l'utilisateur est authentifié, on ajoute les items de base plus les items de connexion
const items = computed(() => isAuth.value ? [
    ...baseItems, // décomposer le tableau baseItems pour ajouter les items de connexion
    { to: '/borrows', label: 'Mes emprunts' },
    { to: '/collection', label: 'Ma collection' }
] : baseItems)

const isMenuOpen = ref(false)

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value
}
</script>