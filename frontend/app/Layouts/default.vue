<template>
    <div class="layout">
        <PreHeader />
        <Header />
        <main class="main">
            <div id="breadcrum"/>
            <slot />
        </main>
    </div>
</template>

<script setup>
</script>

<style scoped>
.layout {
    @apply w-screen h-screen overflow-hidden grid grid-rows-[2em_5em_auto];
}
.main {
    @apply w-full h-full overflow-y-auto py-8;
}
</style>