<template>
    <div class="page-width">
        <!-- Hero Section -->
        <section class="hero">
            <h1>ZeLibrary</h1>
            <p>Votre bibliothèque numérique personnelle</p>
            <button class="btn-primary">Commencer à explorer</button>
        </section>

        <!-- Stats Section -->
        <section class="stats">
            <div class="stat">
                <h3>{{ stats.booksCount || 0 }}</h3>
                <p>Livres disponibles</p>
            </div>
            <div class="stat">
                <h3>{{ stats.usersCount || 0 }}</h3>
                <p>Utilisateurs actifs</p>
            </div>
            <div class="stat">
                <h3>{{ stats.librariesCount || 0 }}</h3>
                <p>Bibliothèques partenaires</p>
            </div>
        </section>

        <!-- Recent Books -->
        <section class="recent-books">
            <h2>Derniers livres ajoutés</h2>
            <div class="books-grid" v-if="recentBooks.length > 0">
                <div v-for="book in recentBooks" :key="book.id" class="book-card">
                    <h4>{{ book.title }}</h4>
                    <p>{{ book.authorsNames }}</p>
                </div>
            </div>
            <p v-else>Chargement des livres...</p>
        </section>

        <!-- Recent Reviews -->
        <section class="recent-reviews">
            <h2>Avis récents</h2>
            <div class="reviews-list" v-if="recentReviews.length > 0">
                <div v-for="review in recentReviews" :key="review.id" class="review-card">
                    <div class="review-header">
                        <h4>{{ review.book.title }}</h4>
                        <div class="rating">{{ '★'.repeat(review.rate) }}</div>
                    </div>
                    <p>{{ review.review }}</p>
                </div>
            </div>
            <p v-else>Chargement des avis...</p>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({
    booksCount: 0,
    usersCount: 0,
    librariesCount: 0
})

const recentBooks = ref([])
const recentReviews = ref([])

onMounted(async () => {
    // TODO: Appeler les APIs pour récupérer les données
    // await loadStats()
    // await loadRecentBooks()
    // await loadRecentReviews()
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