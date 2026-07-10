<template>
  <AdminLayout>
    <div>
      <!-- Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Gestion des commandes</h1>
          <p class="text-gray-600 mt-1">Suivez et gérez toutes les commandes</p>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Total commandes</p>
              <p class="text-3xl font-bold text-gray-800">{{ orders.length }}</p>
            </div>
            <div class="w-12 h-12 bg-gold-pale rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">En attente</p>
              <p class="text-3xl font-bold text-gray-800">{{ pendingOrders }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Terminées</p>
              <p class="text-3xl font-bold text-gray-800">{{ completedOrders }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Revenu total</p>
              <p class="text-3xl font-bold text-gray-800">{{ totalRevenue.toFixed(2) }} MGA</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Search and Filter -->
      <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Rechercher une commande (ID, client...)"
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
            />
          </div>
          <select 
            v-model="statusFilter"
            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
          >
            <option value="all">Tous les statuts</option>
            <option value="pending">En attente</option>
            <option value="processing">En cours</option>
            <option value="delivered">Terminée</option>
            <option value="cancelled">Annulée</option>
          </select>
        </div>
      </div>
      
      <!-- Orders Table -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
        <p class="text-gray-600 mt-4">Chargement...</p>
      </div>
      <div v-else-if="filteredOrders.length === 0" class="bg-white rounded-xl shadow-md p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <p class="text-gray-600 text-lg">Aucune commande trouvée</p>
      </div>
      <div v-else class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Commande</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Client</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4">
                <p class="font-semibold text-gray-800">#{{ order.id }}</p>
                <p class="text-sm text-gray-500">{{ order.items?.length || 0 }} article(s)</p>
              </td>
              <td class="px-6 py-4">
                <p class="font-medium text-gray-800">{{ order.user?.name || 'Client inconnu' }}</p>
                <p class="text-sm text-gray-500">{{ order.user?.email }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="font-semibold text-gray-800">{{ order.total.toFixed(2) }} MGA</p>
              </td>
              <td class="px-6 py-4">
                <select 
                  :value="order.status"
                  @change="updateStatus(order.id, ($event.target as HTMLSelectElement).value)"
                  class="px-3 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-yellow-600 text-sm font-medium"
                  :class="getStatusClass(order.status)"
                >
                  <option value="pending">En attente</option>
                  <option value="processing">En cours</option>
                  <option value="shipped">Expédiée</option>
                  <option value="delivered">Terminée</option>
                  <option value="cancelled">Annulée</option>
                </select>
              </td>
              <td class="px-6 py-4">
                <p class="text-gray-800">{{ new Date(order.created_at).toLocaleDateString('fr-FR') }}</p>
                <p class="text-sm text-gray-500">{{ new Date(order.created_at).toLocaleTimeString('fr-FR') }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button 
                    @click="viewOrder(order)"
                    class="p-2 rounded-lg bg-gold-pale text-gold hover:bg-yellow-100 transition"
                    title="Voir détails"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  <button 
                    @click="printOrder(order)"
                    class="p-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition"
                    title="Imprimer"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Order Details Modal -->
      <div v-if="selectedOrder" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto m-4">
          <div class="p-6 border-b flex justify-between items-center">
            <div>
              <h2 class="text-2xl font-bold text-gray-800">Commande #{{ selectedOrder.id }}</h2>
              <p class="text-gray-500">{{ new Date(selectedOrder.created_at).toLocaleString('fr-FR') }}</p>
            </div>
            <button 
              @click="selectedOrder = null"
              class="p-2 rounded-lg hover:bg-gray-100 transition"
            >
              <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <div class="p-6 space-y-6">
            <!-- Customer Info -->
            <div class="bg-gray-50 rounded-xl p-4">
              <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Informations client
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-500">Nom</p>
                  <p class="font-medium">{{ selectedOrder.user?.name || 'N/A' }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Email</p>
                  <p class="font-medium">{{ selectedOrder.user?.email || 'N/A' }}</p>
                </div>
                <div class="md:col-span-2">
                  <p class="text-sm text-gray-500">Adresse de livraison</p>
                  <p class="font-medium" v-if="selectedOrder.shipping_address">
                    {{ selectedOrder.shipping_address.street }}, {{ selectedOrder.shipping_address.postal_code }} {{ selectedOrder.shipping_address.city }}
                  </p>
                  <p class="font-medium" v-else>N/A</p>
                </div>
              </div>
            </div>

            <!-- Order Items -->
            <div>
              <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Articles commandés
              </h3>
              <div class="bg-gray-50 rounded-xl overflow-hidden">
                <table class="w-full">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Produit</th>
                      <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Quantité</th>
                      <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Prix unitaire</th>
                      <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Total</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <tr v-for="item in selectedOrder.items" :key="item.id" class="bg-white">
                      <td class="px-4 py-3">
                        <p class="font-medium">{{ item.product?.name }}</p>
                        <p class="text-sm text-gray-500">{{ item.product?.brand }}</p>
                      </td>
                      <td class="px-4 py-3">{{ item.quantity }}</td>
                      <td class="px-4 py-3">{{ item.unit_price.toFixed(2) }} MGA</td>
                      <td class="px-4 py-3 font-semibold">{{ (item.unit_price * item.quantity).toFixed(2) }} MGA</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-xl p-4">
              <h3 class="font-semibold text-gray-800 mb-3">Résumé de la commande</h3>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span class="text-gray-600">Sous-total</span>
                  <span class="font-medium">{{ selectedOrder.subtotal?.toFixed(2) || selectedOrder.total.toFixed(2) }} MGA</span>
                </div>
                <div class="flex justify-between" v-if="selectedOrder.discount">
                  <span class="text-gray-600">Remise</span>
                  <span class="font-medium text-green-600">-{{ selectedOrder.discount.toFixed(2) }} MGA</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Livraison</span>
                  <span class="font-medium">{{ selectedOrder.shipping?.toFixed(2) || '0.00' }} MGA</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                  <span>Total</span>
                  <span>{{ selectedOrder.total.toFixed(2) }} MGA</span>
                </div>
              </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-gray-50 rounded-xl p-4">
              <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Paiement
              </h3>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-500">Méthode</p>
                  <p class="font-medium">{{ selectedOrder.payment_method_label || selectedOrder.payment_method || 'Non spécifié' }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Statut</p>
                  <span class="px-2 py-1 rounded-full text-sm font-medium" :class="getStatusClass(selectedOrder.status)">
                    {{ getStatusLabel(selectedOrder.status) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="p-6 border-t flex gap-4">
            <button 
              @click="selectedOrder = null"
              class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold"
            >
              Fermer
            </button>
            <button 
              @click="printOrder(selectedOrder)"
              class="flex-1 bg-yellow-700 text-white py-3 rounded-lg hover:bg-yellow-800 transition font-semibold"
            >
              Imprimer
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { adminApi } from '@/services/admin'
import { useToast } from '@/composables/useToast'

const toast = useToast()
const orders = ref<any[]>([])
const loading = ref(false)
const selectedOrder = ref<any>(null)
const searchQuery = ref('')
const statusFilter = ref('all')

const pendingOrders = computed(() => orders.value.filter(o => o.status === 'pending').length)
const completedOrders = computed(() => orders.value.filter(o => o.status === 'delivered').length)
const totalRevenue = computed(() => orders.value.reduce((acc, o) => acc + (o.status !== 'cancelled' ? o.total : 0), 0))

const filteredOrders = computed(() => {
  let filtered = orders.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(o => 
      o.id.toString().includes(query) || 
      o.user?.name?.toLowerCase().includes(query) ||
      o.user?.email?.toLowerCase().includes(query)
    )
  }
  
  if (statusFilter.value !== 'all') {
    filtered = filtered.filter(o => o.status === statusFilter.value)
  }
  
  return filtered
})

onMounted(async () => {
  await fetchOrders()
})

async function fetchOrders() {
  loading.value = true
  try {
    const { data } = await adminApi.orders.list()
    orders.value = data
  } catch {
    toast.error('Erreur lors du chargement des commandes')
  } finally {
    loading.value = false
  }
}

function getStatusClass(status: string): string {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-gold-pale text-yellow-800',
    shipped: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    pending: 'En attente',
    processing: 'En cours',
    shipped: 'Expédiée',
    completed: 'Terminée',
    delivered: 'Terminée',
    cancelled: 'Annulée'
  }
  return labels[status] || status
}

async function updateStatus(orderId: number, status: string) {
  try {
    const { data } = await adminApi.orders.update(orderId, { status })
    const index = orders.value.findIndex(o => o.id === orderId)
    if (index !== -1) orders.value[index] = data
    toast.success('Statut mis à jour')
  } catch {
    toast.error('Erreur lors de la mise à jour du statut')
    await fetchOrders()
  }
}

function viewOrder(order: any) {
  selectedOrder.value = order
}

function printOrder(order: any) {
  toast.info(`Impression de la commande #${order.id}`)
}
</script>
