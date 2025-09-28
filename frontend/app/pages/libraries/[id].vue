<template>
    <div class="page-width">
        <div v-if="loading" class="loading">
            <p>Chargement de la bibliothèque...</p>
        </div>

        <div v-else-if="error" class="error">
            <p>{{ error }}</p>
            <button @click="loadLibrary" class="btn-retry">Réessayer</button>
        </div>

        <div v-else-if="library" class="library-detail">
            <!-- Header de la bibliothèque -->
            <div class="library-header">
                <div class="library-info">
                    <div v-if="library.logo" class="library-logo">
                        <img :src="library.logo" :alt="library.name" />
                    </div>
                    <div class="library-details">
                        <h1>{{ library.name }}</h1>
                        <p class="address">{{ library.address }}</p>
                        <div v-if="library.description" class="description">
                            <p>{{ library.description }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="library-contact">
                    <div v-if="library.phone" class="contact-item">
                        <span class="icon">📞</span>
                        <span>{{ library.phone }}</span>
                    </div>
                    <div v-if="library.email" class="contact-item">
                        <span class="icon">✉️</span>
                        <a :href="`mailto:${library.email}`">{{ library.email }}</a>
                    </div>
                    <div v-if="library.website" class="contact-item">
                        <span class="icon">🌐</span>
                        <a :href="library.website" target="_blank">{{ library.website }}</a>
                    </div>
                    <div v-if="library.borrowLimit" class="contact-item">
                        <span class="icon">📚</span>
                        <span>Limite d'emprunt: {{ library.borrowLimit }} livres</span>
                    </div>
                </div>
            </div>

            <!-- Collection de livres -->
            <div class="books-section">
                <div class="section-header">
                    <h2>Collection de livres</h2>
                    <p v-if="library.booksCollection">{{ library.booksCollection.length }} livres disponibles</p>
                </div>

                <div v-if="library.booksCollection && library.booksCollection.length > 0" class="books-grid">
                    <div 
                        v-for="book in library.booksCollection" 
                        :key="book.id" 
                        class="book-card"
                        @click="navigateToBook(book.id)"
                    >
                        <div class="book-header">
                            <h3>{{ book.title }}</h3>
                            <p v-if="book.authorsNames" class="author">{{ book.authorsNames }}</p>
                        </div>
                        
                        <div v-if="book.summary" class="book-summary">
                            <p>{{ book.summary.substring(0, 150) }}{{ book.summary.length > 150 ? '...' : '' }}</p>
                        </div>

                        <div class="book-details">
                            <div v-if="book.mediaType" class="detail">
                                <span class="label">Type:</span>
                                <span>{{ book.mediaType }}</span>
                            </div>
                            <div v-if="book.categoriesNames" class="detail">
                                <span class="label">Catégories:</span>
                                <span>{{ book.categoriesNames }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="empty-books">
                    <p>Cette bibliothèque n'a pas encore de livres dans sa collection.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const route = useRoute()
const library = ref(null)
const loading = ref(true)
const error = ref(null)

const loadLibrary = async () => {
    try {
        loading.value = true
        error.value = null
        
        const response = await $fetch(`/api/libraries/${route.params.id}`)
        library.value = response
    } catch (err) {
        console.error('Erreur lors du chargement de la bibliothèque:', err)
        error.value = 'Impossible de charger les informations de cette bibliothèque.'
    } finally {
        loading.value = false
    }
}

const navigateToBook = (bookId) => {
    navigateTo(`/books/${bookId}`)
}

onMounted(() => {
    loadLibrary()
})
</script>

<style scoped>
.loading, .error {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.btn-retry {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 1rem;
}

.library-detail {
    max-width: 1200px;
    margin: 0 auto;
}

.library-header {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.library-info {
    display: flex;
    gap: 2rem;
    margin-bottom: 2rem;
}

.library-logo {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    overflow: hidden;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.library-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.library-details h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.address {
    font-size: 1.1rem;
    color: #6b7280;
    margin-bottom: 1rem;
}

.description {
    color: #4b5563;
    line-height: 1.6;
}

.library-contact {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.contact-item .icon {
    font-size: 1.2rem;
}

.contact-item a {
    color: #3b82f6;
    text-decoration: none;
}

.contact-item a:hover {
    text-decoration: underline;
}

.books-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.section-header {
    margin-bottom: 2rem;
    text-align: center;
}

.section-header h2 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.section-header p {
    color: #6b7280;
    font-size: 1.1rem;
}

.books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.book-card {
    background: #f9fafb;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.3s ease;
}

.book-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background: white;
}

.book-header h3 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.author {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.book-summary {
    margin-bottom: 1rem;
}

.book-summary p {
    color: #4b5563;
    line-height: 1.5;
    font-size: 0.9rem;
}

.book-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.detail {
    display: flex;
    gap: 0.5rem;
    font-size: 0.85rem;
}

.detail .label {
    font-weight: 600;
    color: #374151;
}

.empty-books {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

@media (max-width: 768px) {
    .library-info {
        flex-direction: column;
        text-align: center;
    }
    
    .library-logo {
        align-self: center;
    }
    
    .library-contact {
        grid-template-columns: 1fr;
    }
}
</style>