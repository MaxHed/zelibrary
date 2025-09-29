<template>
    <section class="page-width mx-auto py-6">
        <h1 class="text-2xl font-bold">Mon compte</h1>
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <h2 class="text-lg font-bold">Mes informations</h2>
                <!-- <p class="text-sm opacity-70">Email: {{ user.email }}</p> -->
                 {{ user }}
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { useAuth } from '@/composable/useAuth'
const { user } = useAuth()

</script>