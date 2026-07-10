<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row gap-8">
        <aside class="md:w-72 shrink-0">
          <div class="card space-y-6 sticky top-24">
            <div>
              <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Catégories
              </h2>
              <ul class="space-y-2">
                <li>
                  <button
                    class="w-full text-left px-3 py-2 rounded-lg transition hover:bg-yellow-50"
                    :class="selectedCategory === null ? 'bg-yellow-100 text-yellow-800 font-semibold' : 'text-gray-600'"
                    @click="filterByCategory(null)"
                  >
                    Tous les produits
                  </button>
                </li>
                <li v-for="category in productStore.categories" :key="category.id">
                  <button
                    class="w-full text-left px-3 py-2 rounded-lg transition hover:bg-yellow-50"
                    :class="selectedCategory === category.id ? 'bg-yellow-100 text-yellow-800 font-semibold' : 'text-gray-600'"
                    @click="filterByCategory(category.id)"
                  >
                    {{ category.name }}
                  </button>
                </li>
              </ul>
            </div>

            <div>
              <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Prix
              </h2>
              <div class="space-y-4">
                <div>
                  <label class="text-sm text-gray-600 mb-1 block">Min: {{ formatPrice(minPrice) }}</label>
                  <input
                    v-model.number="minPrice"
                    type="range"
                    min="0"
                    max="1000000"
                    step="10000"
                    class="w-full accent-yellow-600"
                    @input="applyFilters"
                  />
                </div>
                <div>
                  <label class="text-sm text-gray-600 mb-1 block">Max: {{ formatPrice(maxPrice) }}</label>
                  <input
                    v-model.number="maxPrice"
                    type="range"
                    min="0"
                    max="1000000"
                    step="10000"
                    class="w-full accent-yellow-600"
                    @input="applyFilters"
                  />
                </div>
              </div>
            </div>

            <div>
              <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
                Taille
              </h2>
              <select v-model="selectedSize" class="input-field" @change="applyFilters">
                <option value="">Toutes</option>
                <option v-for="size in productStore.getUniqueSizes()" :key="size" :value="size">
                  {{ size }}
                </option>
              </select>
            </div>

            <div>
              <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
                Couleur
              </h2>
              <select v-model="selectedColor" class="input-field" @change="applyFilters">
                <option value="">Toutes</option>
                <option v-for="color in productStore.getUniqueColors()" :key="color" :value="color">
                  {{ color }}
                </option>
              </select>
            </div>

            <div>
              <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Marque
              </h2>
              <select v-model="selectedBrand" class="input-field" @change="applyFilters">
                <option value="">Toutes</option>
                <option v-for="brand in productStore.getUniqueBrands()" :key="brand" :value="brand">
                  {{ brand }}
                </option>
              </select>
            </div>

            <AppButton variant="secondary" class="w-full" @click="resetFilters">
              Réinitialiser les filtres
            </AppButton>
          </div>
        </aside>

        <div class="flex-1">
          <div class="mb-6">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher un produit..."
                class="input-field pl-10"
                @input="onSearchInput"
              />
              <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <div v-if="productStore.loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
            <p class="text-gray-600 mt-4">Chargement...</p>
          </div>
          <div v-else-if="productStore.products.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-600 text-lg">Aucun produit trouvé</p>
            <p class="text-gray-400 text-sm mt-2">Essayez de modifier vos filtres</p>
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <ProductCard
              v-for="product in productStore.products"
              :key="product.id"
              :product="product"
              :show-favorite="authStore.isAuthenticated"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import AppButton from '@/components/ui/AppButton.vue'
import ProductCard from '@/components/ProductCard.vue'
import { useProductStore } from '@/stores/product'
import { useAuthStore } from '@/stores/auth'
import { useFavoritesStore } from '@/stores/favorites'
import { useCurrency } from '@/composables/useCurrency'

const route = useRoute()
const productStore = useProductStore()
const authStore = useAuthStore()
const favoritesStore = useFavoritesStore()
const { formatPrice } = useCurrency()

const selectedCategory = ref<number | null>(null)
const searchQuery = ref('')
const minPrice = ref(0)
const maxPrice = ref(1000000)
const selectedSize = ref('')
const selectedColor = ref('')
const selectedBrand = ref('')

let searchTimeout: ReturnType<typeof setTimeout> | null = null

function getFilters() {
  return {
    minPrice: minPrice.value > 0 ? minPrice.value : undefined,
    maxPrice: maxPrice.value < 1000000 ? maxPrice.value : undefined,
    size: selectedSize.value || undefined,
    color: selectedColor.value || undefined,
    brand: selectedBrand.value || undefined,
  }
}

function fetchWithCurrentFilters() {
  productStore.fetchProducts(
    selectedCategory.value ?? undefined,
    searchQuery.value || undefined,
    getFilters()
  )
}

onMounted(async () => {
  if (route.query.category) {
    selectedCategory.value = Number(route.query.category)
  }
  await productStore.fetchCategories()
  fetchWithCurrentFilters()
  if (authStore.isAuthenticated) {
    favoritesStore.fetchFavorites()
  }
})

function filterByCategory(categoryId: number | null) {
  selectedCategory.value = categoryId
  fetchWithCurrentFilters()
}

function onSearchInput() {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(fetchWithCurrentFilters, 300)
}

function applyFilters() {
  fetchWithCurrentFilters()
}

function resetFilters() {
  minPrice.value = 0
  maxPrice.value = 1000000
  selectedSize.value = ''
  selectedColor.value = ''
  selectedBrand.value = ''
  searchQuery.value = ''
  selectedCategory.value = null
  fetchWithCurrentFilters()
}
</script>
