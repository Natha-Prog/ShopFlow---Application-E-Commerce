<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Mon panier</h1>

      <div v-if="cartStore.loading" class="text-center py-8">
        <p class="text-gray-600">Chargement...</p>
      </div>
      <div v-else-if="cartStore.items.length === 0" class="text-center py-8">
        <p class="text-gray-600">Votre panier est vide</p>
        <router-link to="/catalog" class="btn-primary inline-block mt-4">Voir le catalogue</router-link>
      </div>
      <div v-else>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2 space-y-4">
            <CartItem v-for="item in cartStore.items" :key="item.id" :item="item" />
          </div>

          <div class="card h-fit">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Résumé</h2>
            <div class="flex justify-between mb-2">
              <span class="text-gray-600">Sous-total</span>
              <span class="font-semibold">{{ formatPrice(cartStore.total) }}</span>
            </div>
            <div class="flex justify-between mb-4">
              <span class="text-gray-600">Livraison</span>
              <span class="font-semibold">{{ cartStore.total >= 50 ? 'Gratuite' : formatPrice(5.99) }}</span>
            </div>
            <hr class="mb-4">
            <div class="flex justify-between mb-6">
              <span class="text-xl font-bold">Total</span>
              <span class="text-xl font-bold text-gold">
                {{ formatPrice(cartStore.total + (cartStore.total >= 50 ? 0 : 5.99)) }}
              </span>
            </div>
            <router-link to="/checkout" class="btn-primary block w-full text-center">
              Passer la commande
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import CartItem from '@/components/CartItem.vue'
import { useCartStore } from '@/stores/cart'
import { useCurrency } from '@/composables/useCurrency'

const cartStore = useCartStore()
const { formatPrice } = useCurrency()

onMounted(() => {
  cartStore.fetchCart()
})
</script>
