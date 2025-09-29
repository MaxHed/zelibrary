<template>
    <div class="page-width">
        <div class="header">
            <h1>Bibliothèques partenaires</h1>
            <p>Découvrez les bibliothèques qui participent à notre réseau</p>
        </div>

        <div v-if="loading" class="loading">
            <p>Chargement des bibliothèques...</p>
        </div>

        <div v-else-if="libraries.length === 0" class="empty">
            <p>Aucune bibliothèque disponible pour le moment.</p>
        </div>

        <div v-else class="libraries-grid">
            <div 
                v-for="library in libraries" 
                :key="library.id" 
                class="library-card"
                @click="navigateToLibrary(library.id)"
            >
            {{ library }}
                <div class="library-header">
                    <div v-if="library.logo" class="library-logo">
                        <img :src="library.logo" :alt="library.name" />
                    </div>
                    <div class="library-info">
                        <h3>{{ library.name }}</h3>
                        <p class="library-address">{{ library.address }}</p>
                    </div>
                </div>
                
                <div v-if="library.description" class="library-description">
                    <p>{{ library.description }}</p>
                </div>

                <div class="library-details">
                    <div v-if="library.phone" class="detail">
                        <span class="icon">📞</span>
                        <span>{{ library.phone }}</span>
                    </div>
                    <div v-if="library.email" class="detail">
                        <span class="icon">✉️</span>
                        <span>{{ library.email }}</span>
                    </div>
                    <div v-if="library.website" class="detail">
                        <span class="icon">🌐</span>
                        <a :href="library.website" target="_blank" @click.stop>{{ library.website }}</a>
                    </div>
                    <div v-if="library.borrowLimit" class="detail">
                        <span class="icon">📚</span>
                        <span>Limite: {{ library.borrowLimit }} livres</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useLibraries } from '@/composable/useLibraries'
const { fetchLibraries, libraries, loading, error } = useLibraries()

const loadLibraries = async () => {
    await fetchLibraries()
}

const navigateToLibrary = (id) => {
    navigateTo(`/libraries/${id}`)
}

onMounted(() => {
    loadLibraries()
})
</script>

<style scoped>
.header {
    text-align: center;
    margin-bottom: 3rem;
}

.header h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #333;
}

.header p {
    font-size: 1.1rem;
    color: #666;
}

.loading, .empty {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.libraries-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

.library-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.3s ease;
}

.library-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.library-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.library-logo {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
}

.library-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.library-info h3 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.library-address {
    color: #6b7280;
    font-size: 0.9rem;
}

.library-description {
    margin-bottom: 1rem;
}

.library-description p {
    color: #4b5563;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.library-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #6b7280;
}

.detail .icon {
    font-size: 1rem;
}

.detail a {
    color: #3b82f6;
    text-decoration: none;
}

.detail a:hover {
    text-decoration: underline;
}
</style>