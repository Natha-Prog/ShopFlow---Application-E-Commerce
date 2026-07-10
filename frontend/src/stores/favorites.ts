import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useFavoritesStore = defineStore('favorites', () => {
  const favorites = ref<any[]>([])
  const loading = ref(false)

  async function fetchFavorites() {
    loading.value = true
    try {
      const response = await api.get('/favorites')
      favorites.value = response.data
    } catch {
      favorites.value = []
    } finally {
      loading.value = false
    }
  }

  async function addFavorite(productId: number) {
    await api.post('/favorites', { product_id: productId })
    await fetchFavorites()
  }

  async function removeFavorite(productId: number) {
    await api.delete(`/favorites/${productId}`)
    await fetchFavorites()
  }

  function isFavorite(productId: number) {
    return favorites.value.some((f) => f.id === productId)
  }

  return { favorites, loading, fetchFavorites, addFavorite, removeFavorite, isFavorite }
})
