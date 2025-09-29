<template>
    <NuxtLink :to="to" :class="`${active ? 'active' : 'nav-item'} ${size ?? `!text-${size}`}`">{{ label }}</NuxtLink>
</template>

<script setup>
const props = defineProps({
    to: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    size: {
        type: String,
        default: 'md'
    }
})

const active = ref(false)
const router = useRouter()

watch(() => router.currentRoute.value.path, () => {
    active.value = router.currentRoute.value.path === props.to
})

</script>

<style scoped>
.nav-item {
    @apply text-white hover:text-gray-200 transition-colors hover:underline lg:text-xl md:text-lg sm:text-base;
}
.active {
    @apply nav-item text-white font-bold underline;
}

</style>