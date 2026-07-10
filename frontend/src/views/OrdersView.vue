<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Mes commandes</h1>

      <div v-if="loading" class="text-center py-8">
        <p class="text-gray-600">Chargement...</p>
      </div>
      <div v-else-if="orders.length === 0" class="text-center py-8">
        <p class="text-gray-600">Vous n'avez pas encore de commandes</p>
        <router-link to="/catalog" class="btn-primary inline-block mt-4">Découvrir le catalogue</router-link>
      </div>
      <div v-else class="space-y-6">
        <div v-for="order in orders" :key="order.id" class="card">
          <div class="flex justify-between items-start mb-6">
            <div>
              <h2 class="text-lg font-bold text-gray-800">Commande #{{ order.id }}</h2>
              <p class="text-gray-600">{{ formatDate(order.created_at) }}</p>
            </div>
            <AppBadge :status="order.status" />
          </div>

          <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Suivi de commande</h3>
            <div class="flex items-center justify-between">
              <div
                v-for="(step, index) in orderSteps"
                :key="step.status"
                class="flex flex-col items-center flex-1"
              >
                <div
                  class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold mb-2"
                  :class="getStepClass(order.status, step.status)"
                >
                  {{ getStepIcon(order.status, step.status, index) }}
                </div>
                <span class="text-xs text-center" :class="getStepTextClass(order.status, step.status)">
                  {{ step.label }}
                </span>
              </div>
            </div>
          </div>

          <div class="border-t pt-4 mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Articles</h3>
            <div class="space-y-2">
              <div v-for="item in order.items" :key="item.id" class="flex justify-between items-center">
                <span class="text-gray-600">{{ item.product?.name }} x{{ item.quantity }}</span>
                <span class="font-semibold">{{ formatPrice(item.unit_price * item.quantity) }}</span>
              </div>
            </div>
          </div>

          <div v-if="order.shipping_address" class="border-t pt-4 mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Adresse de livraison</h3>
            <p class="text-gray-600 text-sm">
              {{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}
            </p>
            <p class="text-gray-600 text-sm">{{ order.shipping_address.street }}</p>
            <p class="text-gray-600 text-sm">
              {{ order.shipping_address.postal_code }} {{ order.shipping_address.city }},
              {{ order.shipping_address.country }}
            </p>
          </div>

          <div class="border-t pt-4 flex justify-between items-center">
            <span class="text-gray-600">Total</span>
            <span class="text-xl font-bold text-gold">{{ formatPrice(order.total) }}</span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import api from '@/services/api'
import { useCurrency } from '@/composables/useCurrency'
import { useToast } from '@/composables/useToast'

interface OrderItem {
  id: number
  quantity: number
  unit_price: number
  product?: { name: string }
}

interface Order {
  id: number
  status: string
  created_at: string
  total: number
  shipping_address?: {
    first_name: string
    last_name: string
    street: string
    city: string
    postal_code: string
    country: string
  }
  items: OrderItem[]
}

const orders = ref<Order[]>([])
const loading = ref(false)
const { formatPrice } = useCurrency()
const toast = useToast()

const orderSteps = [
  { status: 'pending', label: 'En attente' },
  { status: 'processing', label: 'Préparation' },
  { status: 'shipped', label: 'Expédiée' },
  { status: 'delivered', label: 'Livrée' },
]

onMounted(async () => {
  loading.value = true
  try {
    const response = await api.get('/orders')
    orders.value = response.data
  } catch {
    toast.error('Impossible de charger vos commandes')
    orders.value = []
  } finally {
    loading.value = false
  }
})

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function getStepClass(orderStatus: string, stepStatus: string) {
  const statusOrder = ['pending', 'processing', 'shipped', 'delivered']
  const orderIndex = statusOrder.indexOf(orderStatus)
  const stepIndex = statusOrder.indexOf(stepStatus)

  if (orderStatus === 'cancelled') return 'bg-gray-200 text-gray-400'
  if (stepIndex <= orderIndex) return 'bg-green-500 text-white'
  return 'bg-gray-200 text-gray-400'
}

function getStepIcon(orderStatus: string, stepStatus: string, index: number) {
  const statusOrder = ['pending', 'processing', 'shipped', 'delivered']
  const orderIndex = statusOrder.indexOf(orderStatus)
  const stepIndex = statusOrder.indexOf(stepStatus)

  if (orderStatus === 'cancelled') return '✕'
  if (stepIndex < orderIndex) return '✓'
  if (stepIndex === orderIndex) return '●'
  return index + 1
}

function getStepTextClass(orderStatus: string, stepStatus: string) {
  const statusOrder = ['pending', 'processing', 'shipped', 'delivered']
  const orderIndex = statusOrder.indexOf(orderStatus)
  const stepIndex = statusOrder.indexOf(stepStatus)

  if (stepIndex <= orderIndex) return 'text-gray-800 font-semibold'
  return 'text-gray-400'
}
</script>
