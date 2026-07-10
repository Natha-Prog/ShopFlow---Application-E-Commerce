<template>
  <div class="card-hover overflow-hidden relative group">
    <button
      v-if="showFavorite"
      type="button"
      class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white/95 shadow-md flex items-center justify-center hover:scale-110 transition-all duration-200"
      :class="isFav ? 'text-red-500' : 'text-gray-400 hover:text-red-400'"
      :aria-label="isFav ? 'Retirer des favoris' : 'Ajouter aux favoris'"
      @click.stop="toggleFavorite"
    >
      <svg class="w-5 h-5" :fill="isFav ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
      </svg>
    </button>
    <router-link :to="`/product/${product.id}`" class="block">
      <div class="relative">
        <img
          :src="product.image_url || 'https://via.placeholder.com/300'"
          :alt="product.name"
          class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300"
        />
        <div v-if="product.stock < 5 && product.stock > 0" class="absolute bottom-3 left-3 bg-yellow-500 text-black text-xs font-bold px-2 py-1 rounded">
          En stock : moins de 5
        </div>
        <div v-if="product.stock === 0" class="absolute bottom-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
          Rupture de stock
        </div>
      </div>
      <div class="p-5">
        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-1 group-hover:text-yellow-600 transition">{{ product.name }}</h3>
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ product.description }}</p>

        <div class="flex items-center gap-2 mb-4 text-sm">
          <span v-if="product.brand" class="bg-yellow-50 text-yellow-800 px-2 py-1 rounded-full font-medium">{{ product.brand }}</span>
          <span v-if="product.color" class="flex items-center gap-1 text-gray-600">
            <span
              class="w-4 h-4 rounded-full border-2 border-gray-200"
              :style="{ backgroundColor: getColorHex(product.color) }"
            ></span>
            {{ product.color }}
          </span>
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
          <span class="text-xl font-bold text-gray-900">{{ formatPrice(product.price) }}</span>
          <span class="text-sm font-semibold text-yellow-600 group-hover:text-yellow-500 transition flex items-center gap-1">
            Voir détails
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </div>
    </router-link>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useCurrency } from '@/composables/useCurrency'
import { useFavoritesStore } from '@/stores/favorites'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'

interface Product {
  id: number
  name: string
  description: string
  price: number
  stock: number
  image_url: string
  category_id: number
  size?: string
  color?: string
  brand?: string
}

const props = defineProps<{
  product: Product
  showFavorite?: boolean
}>()

const { formatPrice } = useCurrency()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()
const toast = useToast()

const isFav = computed(() => favoritesStore.isFavorite(props.product.id))

async function toggleFavorite() {
  if (!authStore.isAuthenticated) {
    toast.warning('Connectez-vous pour gérer vos favoris')
    return
  }
  try {
    if (isFav.value) {
      await favoritesStore.removeFavorite(props.product.id)
      toast.info('Retiré des favoris')
    } else {
      await favoritesStore.addFavorite(props.product.id)
      toast.success('Ajouté aux favoris')
    }
  } catch {
    toast.error('Erreur lors de la mise à jour des favoris')
  }
}

function getColorHex(colorName: string): string {
  const colorMap: Record<string, string> = {
    Noir: '#000000',
    Blanc: '#FFFFFF',
    Rouge: '#FF0000',
    Bleu: '#0000FF',
    Vert: '#00FF00',
    Jaune: '#FFFF00',
    Orange: '#FFA500',
    Violet: '#800080',
    Rose: '#FFC0CB',
    Gris: '#808080',
    Argent: '#C0C0C0',
    Or: '#FFD700',
    Marron: '#8B4513',
    Beige: '#F5F5DC',
  }
  return colorMap[colorName] || '#CCCCCC'
}
</script>
