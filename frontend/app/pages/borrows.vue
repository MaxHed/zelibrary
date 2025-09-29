<template>
    <section class="page-width">
        <h1 class="text-2xl font-bold mb-6">Mes emprunts</h1>
        <!-- table of borrows -->
        <table class="table-auto w-full border-collapse ">
            <thead class="border-b border-gray-200">
                <tr>
                    <th>Livre</th>
                    <th>Auteur</th>
                    <th>Bibliothèque</th>
                    <th>Statut</th>
                    <th>Date d'emprunt</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="borrows.length > 0" v-for="borrow in borrows" :key="borrow.id">
                    <td>{{ borrow.book.title }}</td>
                    <td>{{ borrow.book.authorsNames }}</td>
                    <td>{{ borrow.library.name }}</td>
                    <td>{{ borrow.status }}</td>
                    <td>{{ borrow.createdAt }}</td>
                </tr>
                <tr v-else>
                    <td colspan="4">Aucun emprunt trouvé</td>
                </tr>
            </tbody>
        </table>
    </section>
</template>

<script setup lang="ts">
import { useApi } from '@/composable/useApi'

const { get } = useApi()

const borrows = ref<any[]>([])

onMounted(async () => {
    const res = await get('/borrows', {}, true)
    if (res && typeof res === 'object' && 'member' in res) {
        borrows.value = (res as any).member
    } else {
        borrows.value = []
    }
})
</script>