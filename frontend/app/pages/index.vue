<template>
    <div class="page-width">
        <!-- Hero Section -->
        <section class="hero">
            <h1>ZeLibrary</h1>
            <p>Votre bibliothèque numérique personnelle</p>
            <NuxtLink to="/books" class="btn-primary">Commencer à explorer</NuxtLink>
        </section>


        <!-- Recent Books -->
        <section class="recent-books">
            <h2>Derniers livres ajoutés</h2>
            <div class="books-grid" v-if="recentBooks.length > 0">
                <NuxtLink v-for="book in recentBooks" :key="book.id" class="book-card" :to="`/books/${book.id}`">
                    <h4>{{ book.title }}</h4>
                    <p class="text-sm opacity-80" v-if="book.authorsNames">{{ book.authorsNames }}</p>
                </NuxtLink>
            </div>
            <p v-else>Chargement des livres...</p>
        </section>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '@/composable/useApi'

const { get } = useApi()

const stats = ref({
    booksCount: 0,
    usersCount: 0,
    librariesCount: 0
})

const recentBooks = ref([])
const loading = ref(true)
const error = ref(null)

function extractMemberTotal(res) {
    const member = res && (res['hydra:member'] || res.member) ? (res['hydra:member'] || res.member) : []
    const total = res && (res['hydra:totalItems'] || res.totalItems) ? (res['hydra:totalItems'] || res.totalItems) : member.length
    return { member, total: Number(total) || 0 }
}

async function loadStats() {
    const [booksRes, usersRes, librariesRes] = await Promise.all([
        get('/books?itemsPerPage=1')
    ])
    stats.value.booksCount = extractMemberTotal(booksRes).total
    stats.value.usersCount = extractMemberTotal(usersRes).total
    stats.value.librariesCount = extractMemberTotal(librariesRes).total
}

async function loadRecentBooks() {
    const res = await get('/books', { params: { 'order[createdAt]': 'desc', itemsPerPage: 6 } })
    recentBooks.value = extractMemberTotal(res).member
}


onMounted(async () => {
    try {
        loading.value = true
        await Promise.all([loadStats(), loadRecentBooks(), loadRecentReviews()])
    } catch (e) {
        error.value = e && e.message ? e.message : 'Erreur de chargement'
    } finally {
        loading.value = false
    }
})
</script>

<style scoped>
.hero {
    @apply w-full h-full flex flex-col items-center justify-center p-8 bg-gradient-to-b from-[#667eea] to-[#764ba2] text-white;
}

.hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.hero p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
}



.stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin: 3rem 0;
    text-align: center;
}

.stat h3 {
    font-size: 2.5rem;
    color: #667eea;
    margin-bottom: 0.5rem;
}

.books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.book-card {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.reviews-list {
    display: grid;
    gap: 1rem;
    margin-top: 1rem;
}

.review-card {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.rating {
    color: #ffc107;
}
</style>