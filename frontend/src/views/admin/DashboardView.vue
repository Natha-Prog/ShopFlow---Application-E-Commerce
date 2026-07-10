<template>
  <AdminLayout>
    <!-- Page header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
      <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-gold-dark mb-1">Vue d'ensemble</p>
        <h2 class="text-2xl font-bold text-gray-900">Bonjour, {{ authStore.user?.name?.split(' ')[0] ?? 'Admin' }}</h2>
        <p class="text-sm text-gray-500 mt-1">Suivez les performances de votre boutique en temps réel</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <select
          v-model="timeRange"
          class="input-field text-sm py-2.5 pr-10 min-w-[180px] bg-white shadow-sm"
        >
          <option value="7">7 derniers jours</option>
          <option value="30">30 derniers jours</option>
          <option value="90">90 derniers jours</option>
          <option value="365">Cette année</option>
        </select>
        <router-link to="/admin/orders" class="btn-primary text-sm py-2.5 px-4 min-h-0 shadow-sm">
          Voir les commandes
        </router-link>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <div v-for="n in 4" :key="n" class="admin-stat-card">
          <div class="skeleton h-4 w-24 mb-3" />
          <div class="skeleton h-8 w-32 mb-2" />
          <div class="skeleton h-3 w-20" />
        </div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="admin-panel p-6"><div class="skeleton h-64 w-full rounded-xl" /></div>
        <div class="admin-panel p-6"><div class="skeleton h-64 w-full rounded-xl" /></div>
      </div>
    </div>

    <div v-else class="space-y-6">
      <!-- KPI cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <div v-for="kpi in kpiCards" :key="kpi.label" class="admin-stat-card group">
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <p class="text-xs font-medium uppercase tracking-wide text-gray-500 mb-1">{{ kpi.label }}</p>
              <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ kpi.value }}</p>
              <p class="text-xs text-gray-500 mt-2">{{ kpi.sub }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-105" :class="kpi.iconBg">
              <svg class="w-5 h-5" :class="kpi.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="kpi.icon" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Sales chart -->
        <div class="admin-panel lg:col-span-3">
          <div class="admin-panel-header">
            <div>
              <h3 class="admin-panel-title">Évolution des ventes</h3>
              <p class="text-xs text-gray-500 mt-0.5">Chiffre d'affaires sur la période sélectionnée</p>
            </div>
            <span class="text-sm font-semibold text-gold tabular-nums">{{ stats.totalSales.toFixed(2) }} MGA</span>
          </div>
          <div class="p-6">
            <div v-if="salesData.length === 0" class="h-56 flex flex-col items-center justify-center text-gray-400">
              <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              <p class="text-sm">Aucune vente sur cette période</p>
            </div>
            <div v-else class="h-56 flex items-end justify-between gap-1.5">
              <div
                v-for="(month, index) in salesData"
                :key="index"
                class="flex flex-col items-center flex-1 h-full justify-end group"
              >
                <div
                  class="w-full max-w-[48px] mx-auto bg-gradient-to-t from-yellow-700 via-yellow-500 to-amber-400 rounded-t-md transition-all duration-300 group-hover:from-yellow-600 group-hover:to-amber-300 relative min-h-[4px]"
                  :style="{ height: `${Math.max((month.value / maxSales) * 100, 4)}%` }"
                >
                  <div class="absolute -top-9 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">
                    {{ month.value.toFixed(0) }} MGA
                  </div>
                </div>
                <span class="text-[10px] text-gray-500 mt-2 font-medium truncate w-full text-center">{{ month.label }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Order status -->
        <div class="admin-panel lg:col-span-2">
          <div class="admin-panel-header">
            <div>
              <h3 class="admin-panel-title">Statut des commandes</h3>
              <p class="text-xs text-gray-500 mt-0.5">{{ stats.totalOrders }} commandes au total</p>
            </div>
          </div>
          <div class="p-6 space-y-4">
            <div v-if="orderStatusData.length === 0" class="text-sm text-gray-400 text-center py-8">Aucune commande</div>
            <div v-for="status in orderStatusData" :key="status.label" class="space-y-1.5">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 font-medium">{{ status.label }}</span>
                <span class="font-semibold text-gray-900 tabular-nums">{{ status.count }}</span>
              </div>
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-500"
                  :class="status.color"
                  :style="{ width: `${(status.count / maxOrders) * 100}%` }"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Secondary metrics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="admin-panel">
          <div class="admin-panel-header">
            <h3 class="admin-panel-title">Catégories populaires</h3>
          </div>
          <div class="p-5 space-y-3">
            <div v-for="cat in topCategories" :key="cat.name" class="flex items-center justify-between py-1">
              <span class="text-sm text-gray-700">{{ cat.name }}</span>
              <span class="text-sm font-bold text-gold tabular-nums">{{ cat.count }}</span>
            </div>
            <p v-if="topCategories.length === 0" class="text-sm text-gray-400 text-center py-4">Aucune donnée disponible</p>
          </div>
        </div>

        <div class="admin-panel">
          <div class="admin-panel-header">
            <h3 class="admin-panel-title">Alertes stock</h3>
            <span v-if="lowStockProducts.length" class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">{{ lowStockProducts.length }}</span>
          </div>
          <div class="p-5 space-y-3 max-h-48 overflow-y-auto">
            <div v-for="item in lowStockProducts" :key="item.id" class="flex items-center justify-between gap-2 py-1">
              <span class="text-sm text-gray-700 truncate">{{ item.name }}</span>
              <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full shrink-0">{{ item.stock }} restants</span>
            </div>
            <p v-if="lowStockProducts.length === 0" class="text-sm text-green-600 text-center py-4 flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
              Stock suffisant
            </p>
          </div>
        </div>

        <div class="admin-panel">
          <div class="admin-panel-header">
            <h3 class="admin-panel-title">Commandes livrées</h3>
          </div>
          <div class="p-5 flex flex-col items-center justify-center py-8">
            <p class="text-4xl font-bold text-gray-900 tabular-nums">{{ stats.completedOrders }}</p>
            <p class="text-sm text-gray-500 mt-2">livraisons confirmées</p>
            <div class="mt-4 w-full bg-gray-100 rounded-full h-2">
              <div
                class="h-2 rounded-full bg-gradient-to-r from-green-500 to-emerald-400 transition-all duration-500"
                :style="{ width: stats.totalOrders ? `${(stats.completedOrders / stats.totalOrders) * 100}%` : '0%' }"
              />
            </div>
            <p class="text-xs text-gray-400 mt-2">
              {{ stats.totalOrders ? Math.round((stats.completedOrders / stats.totalOrders) * 100) : 0 }}% du total
            </p>
          </div>
        </div>
      </div>

      <!-- Recent data tables -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent orders -->
        <div class="admin-panel">
          <div class="admin-panel-header">
            <h3 class="admin-panel-title">Commandes récentes</h3>
            <router-link to="/admin/orders" class="text-xs font-semibold text-gold hover:text-gold-dark transition">Tout voir →</router-link>
          </div>
          <div class="divide-y divide-gray-50">
            <div
              v-for="order in recentOrders"
              :key="order.id"
              class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-gray-50/80 transition cursor-pointer"
              @click="$router.push('/admin/orders')"
            >
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-lg bg-gold-pale flex items-center justify-center shrink-0">
                  <span class="text-xs font-bold text-gold-dark">#{{ order.id }}</span>
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-semibold text-gray-900">Commande #{{ order.id }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ order.user?.name || 'Client inconnu' }}</p>
                </div>
              </div>
              <div class="text-right shrink-0">
                <p class="text-sm font-bold text-gray-900 tabular-nums">{{ order.total.toFixed(2) }} MGA</p>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold" :class="getStatusClass(order.status)">
                  {{ getStatusLabel(order.status) }}
                </span>
              </div>
            </div>
            <p v-if="recentOrders.length === 0" class="text-sm text-gray-400 text-center py-10">Aucune commande récente</p>
          </div>
        </div>

        <!-- Top products -->
        <div class="admin-panel">
          <div class="admin-panel-header">
            <h3 class="admin-panel-title">Produits les plus vendus</h3>
            <router-link to="/admin/products" class="text-xs font-semibold text-gold hover:text-gold-dark transition">Tout voir →</router-link>
          </div>
          <div class="divide-y divide-gray-50">
            <div
              v-for="(product, index) in topProducts"
              :key="product.id"
              class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/80 transition cursor-pointer"
              @click="$router.push('/admin/products')"
            >
              <span class="text-xs font-bold text-gray-400 w-4 tabular-nums">{{ index + 1 }}</span>
              <img
                :src="product.image_url || '/placeholder-product.png'"
                :alt="product.name"
                class="w-10 h-10 object-cover rounded-lg bg-gray-100 shrink-0"
              />
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ product.name }}</p>
                <p class="text-xs text-gray-500">{{ product.sales }} vendus</p>
              </div>
              <span class="text-sm font-bold text-gold tabular-nums shrink-0">{{ product.revenue.toFixed(2) }} MGA</span>
            </div>
            <p v-if="topProducts.length === 0" class="text-sm text-gray-400 text-center py-10">Aucun produit vendu</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { adminApi } from '@/services/admin'
import { useToast } from '@/composables/useToast'
import { useAuthStore } from '@/stores/auth'

const toast = useToast()
const authStore = useAuthStore()
const stats = ref({
  totalSales: 0,
  totalOrders: 0,
  totalProducts: 0,
  totalUsers: 0,
  pendingOrders: 0,
  lowStock: 0,
  completedOrders: 0,
})
const loading = ref(false)
const timeRange = ref('30')

const salesData = ref<{ label: string; value: number }[]>([])
const orderStatusData = ref<{ label: string; count: number; color: string }[]>([])
const topCategories = ref<{ name: string; count: number }[]>([])
const lowStockProducts = ref<{ id: number; name: string; stock: number }[]>([])
const recentOrders = ref<any[]>([])
const topProducts = ref<{ id: number; name: string; sales: number; revenue: number; image_url?: string }[]>([])

const statusConfig: Record<string, { label: string; color: string }> = {
  pending: { label: 'En attente', color: 'bg-yellow-400' },
  processing: { label: 'En cours', color: 'bg-amber-500' },
  shipped: { label: 'Expédiée', color: 'bg-purple-500' },
  delivered: { label: 'Livrée', color: 'bg-green-500' },
  cancelled: { label: 'Annulée', color: 'bg-red-400' },
}

const maxSales = computed(() => Math.max(...salesData.value.map(m => m.value), 1))
const maxOrders = computed(() => Math.max(...orderStatusData.value.map(s => s.count), 1))

const kpiCards = computed(() => [
  {
    label: 'Chiffre d\'affaires',
    value: `${stats.value.totalSales.toFixed(2)} MGA`,
    sub: `${stats.value.totalOrders} commandes sur la période`,
    icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    iconBg: 'bg-gold-pale',
    iconColor: 'text-gold-dark',
  },
  {
    label: 'Commandes',
    value: String(stats.value.totalOrders),
    sub: `${stats.value.pendingOrders} en attente de traitement`,
    icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
    iconBg: 'bg-green-50',
    iconColor: 'text-green-600',
  },
  {
    label: 'Produits actifs',
    value: String(stats.value.totalProducts),
    sub: `${stats.value.lowStock} produit(s) en stock faible`,
    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    iconBg: 'bg-purple-50',
    iconColor: 'text-purple-600',
  },
  {
    label: 'Clients',
    value: String(stats.value.totalUsers),
    sub: 'Utilisateurs enregistrés',
    icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    iconBg: 'bg-orange-50',
    iconColor: 'text-orange-600',
  },
])

onMounted(() => fetchDashboard())
watch(timeRange, () => fetchDashboard())

async function fetchDashboard() {
  loading.value = true
  try {
    const days = parseInt(timeRange.value, 10)
    const { data } = await adminApi.dashboard(days)

    stats.value = {
      totalSales: data.total_sales ?? 0,
      totalOrders: data.total_orders ?? 0,
      totalProducts: data.total_products ?? 0,
      totalUsers: data.total_users ?? 0,
      pendingOrders: data.pending_orders ?? 0,
      lowStock: Array.isArray(data.low_stock) ? data.low_stock.length : 0,
      completedOrders: data.completed_orders ?? 0,
    }

    salesData.value = (data.sales_by_day ?? []).map((d: { date: string; total: number }) => ({
      label: new Date(d.date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }),
      value: Number(d.total),
    }))

    orderStatusData.value = Object.entries(data.status_breakdown ?? {}).map(([status, count]) => ({
      label: statusConfig[status]?.label ?? status,
      count: Number(count),
      color: statusConfig[status]?.color ?? 'bg-gray-400',
    }))

    lowStockProducts.value = data.low_stock ?? []
    recentOrders.value = data.recent_orders ?? []
    topProducts.value = (data.top_products ?? []).map((p: any) => ({
      id: p.id,
      name: p.name,
      sales: p.sales ?? 0,
      revenue: p.revenue ?? 0,
    }))
    topCategories.value = []
  } catch {
    toast.error('Erreur lors du chargement du tableau de bord')
  } finally {
    loading.value = false
  }
}

function getStatusClass(status: string) {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-amber-100 text-amber-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

function getStatusLabel(status: string) {
  const labels: Record<string, string> = {
    pending: 'En attente',
    processing: 'En cours',
    shipped: 'Expédiée',
    delivered: 'Livrée',
    cancelled: 'Annulée',
  }
  return labels[status] || status
}
</script>
