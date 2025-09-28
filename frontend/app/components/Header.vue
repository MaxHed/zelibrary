<template>
    <header class="header-container">
        <!-- <PreHeader /> -->
        <div class="h-full page-width flex justify-between items-center  ">
            <div class="flex items-center gap-4 relative h-full w-[30em] py-2">
                <NuxtLink to="/">
                    <NuxtImg src="/logo.svg" alt="Logo" class="logo" />
                </NuxtLink>
            </div>
            <NavBar />
        </div>
    </header>
</template>

<script setup>
import NavBar from '@/components/UI/NavBar.vue';
</script>

<style scoped>
.header-container {
    @apply w-screen max-h-full bg-[var(--color-primary)] h-[5em];
}
.logo {
    @apply h-full;
}
</style>