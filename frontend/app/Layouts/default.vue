<template>
    <div class="layout">
        <PreHeader />
        <Header />
        <main class="main">
            <div class="page-width h-full">
                <div id="breadcrum"/>
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
</script>

<style scoped>
.layout {
    @apply w-screen h-screen flex flex-col overflow-y-auto overflow-x-hidden;
}
.main {
    @apply w-full h-full  py-8;
}

</style>