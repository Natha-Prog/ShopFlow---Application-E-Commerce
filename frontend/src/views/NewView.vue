<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Nouveautés</h1>
      
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
        <p class="text-gray-600 mt-4">Chargement...</p>
      </div>

      <div v-else-if="newProducts.length === 0" class="text-center py-12">
        <p class="text-gray-600 text-lg">Aucune nouveauté pour le moment</p>
        <router-link to="/catalog" class="inline-block mt-4 btn-primary">Voir le catalogue</router-link>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in newProducts"
          :key="product.id"
          :product="product"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import ProductCard from '@/components/ProductCard.vue'
import { useProductStore } from '@/stores/product'

const productStore = useProductStore()

const loading = ref(false)
const newProducts = ref<any[]>([])

onMounted(async () => {
  loading.value = true
  try {
    await productStore.fetchProducts()
    // Get the latest 8 products as new arrivals
    newProducts.value = productStore.products.slice(0, 8)
  } catch {
    newProducts.value = []
  } finally {
    loading.value = false
  }
})
</script>
