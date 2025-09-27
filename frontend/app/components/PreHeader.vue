<template>
    <div class="h-full w-full pre-header">
        <div class="page-width w-full flex justify-end items-center">
            <div class="pre-header-content">
                <AuthLink />
            </div>
        </div>
    </div>
</template>

<script setup>
import AuthLink from './Auth/AuthLink.vue';
</script>


<style>
.pre-header {
    @apply h-full flex justify-end items-center bg-gray-900 ;
}
</style>