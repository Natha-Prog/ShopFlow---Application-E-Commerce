<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-12 max-w-lg">
      <div class="card text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Commande confirmée !</h1>
        <p class="text-gray-600 mb-2">Merci pour votre achat.</p>
        <p class="text-gray-800 font-semibold mb-6">Commande #{{ orderId }}</p>

        <div v-if="orderLoading" class="skeleton h-24 w-full rounded-lg mb-6" />

        <div v-else-if="order" class="text-left mb-6 space-y-4">
          <div class="p-4 bg-gold-pale rounded-lg border border-yellow-200">
            <p class="text-sm text-gray-600">Total à régler</p>
            <p class="text-2xl font-bold text-gold">{{ formatPrice(order.total) }}</p>
          </div>

          <div v-if="order.payment_method_label" class="p-4 bg-gray-50 rounded-lg border text-sm space-y-2">
            <p class="font-semibold text-gray-800">Mode de paiement : {{ order.payment_method_label }}</p>
            <p v-if="order.payment_instructions" class="text-gray-600">{{ order.payment_instructions }}</p>
            <p v-if="order.payment_merchant_number" class="text-gold font-bold text-lg">{{ order.payment_merchant_number }}</p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <router-link :to="`/orders`" class="btn-primary">Voir mes commandes</router-link>
          <router-link to="/catalog" class="btn-secondary">Continuer mes achats</router-link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import api from '@/services/api'
import { useCurrency } from '@/composables/useCurrency'

interface OrderSummary {
  id: number
  total: number
  payment_method_label?: string
  payment_instructions?: string
  payment_merchant_number?: string
}

const route = useRoute()
const { formatPrice } = useCurrency()
const orderId = computed(() => route.params.id)
const order = ref<OrderSummary | null>(null)
const orderLoading = ref(true)

onMounted(async () => {
  try {
    const { data } = await api.get(`/orders/${orderId.value}`)
    order.value = data
  } catch {
    order.value = null
  } finally {
    orderLoading.value = false
  }
})
</script>
