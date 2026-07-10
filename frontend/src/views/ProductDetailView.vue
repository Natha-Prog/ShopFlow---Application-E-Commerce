<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <div v-if="productStore.loading" class="text-center py-8">
        <p class="text-gray-600">Chargement...</p>
      </div>
      <div v-else-if="productStore.currentProduct" class="card p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <img
              :src="productStore.currentProduct.image_url || 'https://via.placeholder.com/400'"
              :alt="productStore.currentProduct.name"
              class="w-full h-96 object-cover rounded-lg"
            />
          </div>
          <div>
            <div class="flex items-start justify-between gap-4 mb-4">
              <h1 class="text-3xl font-bold text-gray-800">{{ productStore.currentProduct.name }}</h1>
              <button
                v-if="authStore.isAuthenticated"
                type="button"
                class="w-10 h-10 rounded-full border flex items-center justify-center hover:scale-110 transition shrink-0"
                :class="isFavorite ? 'text-red-500 border-red-200 bg-red-50' : 'text-gray-400 border-gray-200'"
                @click="toggleFavorite"
              >
                <svg class="w-5 h-5" :fill="isFavorite ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </button>
            </div>
            <p class="text-2xl text-gold font-bold mb-4">{{ formatPrice(productStore.currentProduct.price) }}</p>

            <div v-if="productStore.currentProduct.brand" class="mb-4">
              <span class="text-gray-600">Marque: </span>
              <span class="font-semibold">{{ productStore.currentProduct.brand }}</span>
            </div>

            <p class="text-gray-600 mb-6">{{ productStore.currentProduct.description }}</p>
            <p class="text-gray-600 mb-6">Stock: {{ productStore.currentProduct.stock }}</p>

            <div v-if="productStore.currentProduct.size" class="mb-4">
              <label class="text-gray-700 block mb-2">Taille:</label>
              <span class="bg-gray-100 px-3 py-2 rounded-lg inline-block">{{ productStore.currentProduct.size }}</span>
            </div>

            <div v-if="productStore.currentProduct.color" class="mb-6">
              <label class="text-gray-700 block mb-2">Couleur:</label>
              <div class="flex items-center gap-2">
                <span
                  class="w-8 h-8 rounded-full border-2 border-gray-300"
                  :style="{ backgroundColor: getColorHex(productStore.currentProduct.color) }"
                ></span>
                <span>{{ productStore.currentProduct.color }}</span>
              </div>
            </div>

            <div class="flex items-center gap-4 mb-6">
              <label class="text-gray-700">Quantité:</label>
              <input
                v-model.number="quantity"
                type="number"
                min="1"
                :max="productStore.currentProduct.stock"
                class="input-field w-20"
              />
            </div>

            <AppButton
              class="w-full"
              :disabled="!authStore.isAuthenticated || productStore.currentProduct.stock === 0"
              @click="addToCart"
            >
              {{ !authStore.isAuthenticated ? 'Connectez-vous pour ajouter au panier' : 'Ajouter au panier' }}
            </AppButton>
          </div>
        </div>

        <div class="mt-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">Avis clients</h2>

          <div class="card mb-6">
            <div class="flex items-center gap-6">
              <div class="text-center">
                <p class="text-4xl font-bold text-gray-800">{{ averageRating.toFixed(1) }}</p>
                <div class="flex text-yellow-400 mt-1">
                  <span v-for="i in 5" :key="i">{{ i <= Math.round(averageRating) ? '★' : '☆' }}</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ reviews.length }} avis</p>
              </div>
              <div class="flex-1 space-y-2">
                <div v-for="i in 5" :key="i" class="flex items-center gap-2">
                  <span class="text-sm text-gray-600 w-8">{{ 6 - i }} ★</span>
                  <div class="flex-1 bg-gray-200 rounded-full h-3">
                    <div
                      class="bg-yellow-400 h-3 rounded-full"
                      :style="{ width: `${getRatingPercentage(6 - i)}%` }"
                    ></div>
                  </div>
                  <span class="text-sm text-gray-600 w-8">{{ getRatingCount(6 - i) }}</span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="authStore.isAuthenticated" class="card mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Donner votre avis</h3>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Note</label>
              <div class="flex gap-2">
                <button
                  v-for="i in 5"
                  :key="i"
                  type="button"
                  class="text-3xl transition hover:scale-110"
                  :class="i <= newReview.rating ? 'text-yellow-400' : 'text-gray-300'"
                  @click="newReview.rating = i"
                >
                  ★
                </button>
              </div>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Commentaire</label>
              <textarea
                v-model="newReview.comment"
                rows="3"
                class="input-field"
                placeholder="Partagez votre expérience avec ce produit..."
              ></textarea>
            </div>
            <AppButton :loading="submittingReview" :disabled="newReview.rating === 0" @click="submitReview">
              Publier l'avis
            </AppButton>
          </div>
          <div v-else class="card mb-6 text-center">
            <p class="text-gray-600">Connectez-vous pour laisser un avis</p>
            <router-link to="/login" class="inline-block mt-2 text-gold hover:underline">
              Se connecter
            </router-link>
          </div>

          <div class="space-y-4">
            <div v-for="review in reviews" :key="review.id" class="card">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gold-pale rounded-full flex items-center justify-center text-gold font-bold">
                    {{ review.user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-semibold">{{ review.user.name }}</p>
                    <p class="text-sm text-gray-600">{{ formatReviewDate(review.created_at) }}</p>
                  </div>
                </div>
                <div class="flex text-yellow-400">
                  <span v-for="i in 5" :key="i">{{ i <= review.rating ? '★' : '☆' }}</span>
                </div>
              </div>
              <p class="text-gray-700">{{ review.comment }}</p>
            </div>
            <p v-if="reviews.length === 0" class="text-center text-gray-500 py-4">Aucun avis pour ce produit</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import AppButton from '@/components/ui/AppButton.vue'
import api from '@/services/api'
import { useProductStore } from '@/stores/product'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useFavoritesStore } from '@/stores/favorites'
import { useCurrency } from '@/composables/useCurrency'
import { useToast } from '@/composables/useToast'

interface Review {
  id: number
  rating: number
  comment: string
  created_at: string
  user: { id: number; name: string }
}

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()
const cartStore = useCartStore()
const authStore = useAuthStore()
const favoritesStore = useFavoritesStore()
const { formatPrice } = useCurrency()
const toast = useToast()

const quantity = ref(1)
const reviews = ref<Review[]>([])
const submittingReview = ref(false)

const newReview = ref({
  rating: 0,
  comment: '',
})

const productId = computed(() => Number(route.params.id))
const isFavorite = computed(() => favoritesStore.isFavorite(productId.value))

const averageRating = computed(() => {
  if (reviews.value.length === 0) return productStore.currentProduct?.average_rating ?? 0
  const sum = reviews.value.reduce((acc, r) => acc + r.rating, 0)
  return sum / reviews.value.length
})

onMounted(async () => {
  await productStore.fetchProduct(productId.value)
  syncReviewsFromProduct()
  if (authStore.isAuthenticated) {
    favoritesStore.fetchFavorites()
  }
})

watch(
  () => productStore.currentProduct,
  () => syncReviewsFromProduct()
)

function syncReviewsFromProduct() {
  if (productStore.currentProduct?.reviews) {
    reviews.value = productStore.currentProduct.reviews as Review[]
  }
}

function formatReviewDate(date: string) {
  return new Date(date).toLocaleDateString('fr-FR')
}

function getRatingCount(rating: number): number {
  return reviews.value.filter((r) => r.rating === rating).length
}

function getRatingPercentage(rating: number): number {
  if (reviews.value.length === 0) return 0
  return (getRatingCount(rating) / reviews.value.length) * 100
}

async function submitReview() {
  if (!authStore.isAuthenticated || newReview.value.rating === 0) return

  submittingReview.value = true
  try {
    const response = await api.post('/reviews', {
      product_id: productId.value,
      rating: newReview.value.rating,
      comment: newReview.value.comment || null,
    })
    reviews.value = [response.data, ...reviews.value.filter((r) => r.id !== response.data.id)]
    newReview.value = { rating: 0, comment: '' }
    toast.success('Avis publié avec succès')
  } catch {
    toast.error('Erreur lors de la publication de l\'avis')
  } finally {
    submittingReview.value = false
  }
}

async function toggleFavorite() {
  try {
    if (isFavorite.value) {
      await favoritesStore.removeFavorite(productId.value)
      toast.info('Retiré des favoris')
    } else {
      await favoritesStore.addFavorite(productId.value)
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

async function addToCart() {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }

  await cartStore.addToCart(productStore.currentProduct!.id, quantity.value)
  toast.success('Produit ajouté au panier')
}
</script>
