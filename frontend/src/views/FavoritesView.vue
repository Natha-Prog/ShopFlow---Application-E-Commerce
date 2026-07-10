<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Mes favoris</h1>

      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
        <p class="text-gray-600 mt-4">Chargement...</p>
      </div>

      <div v-else-if="favorites.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
        </svg>
        <p class="text-gray-600 text-lg">Aucun favori pour le moment</p>
        <router-link to="/catalog" class="inline-block mt-4 btn-primary">Parcourir le catalogue</router-link>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in favorites"
          :key="product.id"
          :product="product"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import ProductCard from '@/components/ProductCard.vue'
import { useFavoritesStore } from '@/stores/favorites'
import { useAuthStore } from '@/stores/auth'

const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()

const { favorites, loading, fetchFavorites } = favoritesStore

onMounted(async () => {
  if (authStore.isAuthenticated) {
    await fetchFavorites()
  }
})
</script>
