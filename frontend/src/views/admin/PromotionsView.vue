<template>
  <AdminLayout>
    <div>
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Gestion des Promotions</h1>
      
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add/Edit Promotion Form -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">
            {{ editingPromo ? 'Modifier la promotion' : 'Nouvelle promotion' }}
          </h2>
          <form @submit.prevent="savePromotion">
            <div class="space-y-4">
              <div>
                <label class="block text-gray-700 mb-2">Code</label>
                <input 
                  v-model="promoForm.code"
                  type="text" 
                  required
                  :disabled="!!editingPromo"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600 uppercase"
                  placeholder="PROMO20"
                />
              </div>
              
              <div>
                <label class="block text-gray-700 mb-2">Type de réduction</label>
                <select 
                  v-model="promoForm.type"
                  required
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                >
                  <option value="percentage">Pourcentage (%)</option>
                  <option value="fixed">Montant fixe (MGA)</option>
                </select>
              </div>
              
              <div>
                <label class="block text-gray-700 mb-2">Valeur</label>
                <input 
                  v-model.number="promoForm.value"
                  type="number" 
                  required
                  min="0"
                  :max="promoForm.type === 'percentage' ? 100 : undefined"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                  :placeholder="promoForm.type === 'percentage' ? '20' : '10'"
                />
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-gray-700 mb-2">Date de début</label>
                  <input 
                    v-model="promoForm.starts_at"
                    type="date" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                  />
                </div>
                <div>
                  <label class="block text-gray-700 mb-2">Date de fin</label>
                  <input 
                    v-model="promoForm.ends_at"
                    type="date" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                  />
                </div>
              </div>
              
              <div>
                <label class="block text-gray-700 mb-2">Utilisations max</label>
                <input 
                  v-model.number="promoForm.max_uses"
                  type="number" 
                  min="1"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                  placeholder="Illimité si vide"
                />
              </div>
              
              <div>
                <label class="block text-gray-700 mb-2">Minimum d'achat (MGA)</label>
                <input 
                  v-model.number="promoForm.min_purchase"
                  type="number" 
                  min="0"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                  placeholder="0"
                />
              </div>
              
              <div class="flex gap-2">
                <button 
                  type="submit"
                  class="flex-1 bg-yellow-700 text-white py-2 rounded-lg hover:bg-yellow-800 transition"
                >
                  {{ editingPromo ? 'Modifier' : 'Créer' }}
                </button>
                <button 
                  v-if="editingPromo"
                  @click="cancelEdit"
                  type="button"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                  Annuler
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Promotions List -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Promotions</h2>
          
          <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
          </div>
          <div v-else-if="promotions.length === 0" class="text-center py-8 text-gray-600">
            Aucune promotion créée
          </div>
          
          <div v-else class="space-y-4">
            <div 
              v-for="promo in promotions" 
              :key="promo.id"
              class="border rounded-lg p-4"
              :class="promo.is_active ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50'"
            >
              <div class="flex justify-between items-start">
                <div>
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl font-bold text-gold">{{ promo.code }}</span>
                    <span 
                      class="px-2 py-1 rounded-full text-xs font-semibold"
                      :class="promo.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                      {{ promo.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ promo.type === 'percentage' ? promo.value + '%' : promo.value + 'MGA' }} de réduction
                  </p>
                  <p v-if="promo.starts_at || promo.ends_at" class="text-sm text-gray-500">
                    <span v-if="promo.starts_at">Du {{ formatDate(promo.starts_at) }}</span>
                    <span v-if="promo.ends_at"> au {{ formatDate(promo.ends_at) }}</span>
                  </p>
                  <div class="flex gap-4 mt-2 text-sm text-gray-600">
                    <span>Utilisations: {{ promo.used_count ?? 0 }}/{{ promo.max_uses || '∞' }}</span>
                    <span v-if="promo.min_purchase">Min: {{ promo.min_purchase }}MGA</span>
                  </div>
                </div>
                <div class="flex gap-2">
                  <button 
                    @click="editPromotion(promo)"
                    class="p-2 text-gold hover:bg-gold-pale rounded-lg transition"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button 
                    @click="togglePromoStatus(promo)"
                    class="p-2 rounded-lg transition"
                    :class="promo.is_active ? 'text-red-600 hover:bg-red-100' : 'text-green-600 hover:bg-green-100'"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 0A9 9 0 015.636 18.364m12.728-12.728L5.636 18.364"></path>
                    </svg>
                  </button>
                  <button 
                    @click="deletePromotion(promo.id)"
                    class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-gray-600 mb-2">Promotions actives</h3>
          <p class="text-3xl font-bold text-green-600">{{ activePromosCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-gray-600 mb-2">Total utilisations</h3>
          <p class="text-3xl font-bold text-gold">{{ totalUses }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-gray-600 mb-2">Total promotions</h3>
          <p class="text-3xl font-bold text-purple-600">{{ promotions.length }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-gray-600 mb-2">Taux d'utilisation</h3>
          <p class="text-3xl font-bold text-orange-600">{{ usageRate.toFixed(1) }}%</p>
        </div>
      </div>
    </div>

    <!-- Delete confirmation -->
    <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Supprimer la promotion</h2>
        <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cette promotion ?</p>
        <div class="flex gap-4">
          <button @click="confirmDelete" class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold">Supprimer</button>
          <button @click="deleteTarget = null" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">Annuler</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { adminApi } from '@/services/admin'
import { useToast } from '@/composables/useToast'

interface Promotion {
  id: number
  code: string
  type: 'percentage' | 'fixed'
  value: number
  starts_at?: string
  ends_at?: string
  max_uses?: number
  used_count?: number
  min_purchase?: number
  is_active: boolean
}

const toast = useToast()
const promotions = ref<Promotion[]>([])
const editingPromo = ref<Promotion | null>(null)
const loading = ref(false)
const deleteTarget = ref<number | null>(null)

const promoForm = ref({
  code: '',
  type: 'percentage' as 'percentage' | 'fixed',
  value: 0,
  starts_at: '',
  ends_at: '',
  max_uses: undefined as number | undefined,
  min_purchase: 0,
})

const activePromosCount = computed(() => promotions.value.filter(p => p.is_active).length)
const totalUses = computed(() => promotions.value.reduce((acc, p) => acc + (p.used_count || 0), 0))
const usageRate = computed(() => {
  const totalMax = promotions.value.reduce((acc, p) => acc + (p.max_uses || 0), 0)
  if (totalMax === 0) return 0
  return (totalUses.value / totalMax) * 100
})

onMounted(() => {
  loadPromotions()
})

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('fr-FR')
}

function toDateInput(date?: string): string {
  if (!date) return ''
  return date.split('T')[0] ?? ''
}

function todayInput(): string {
  return new Date().toISOString().slice(0, 10)
}

async function loadPromotions() {
  loading.value = true
  try {
    const { data } = await adminApi.promotions.list()
    promotions.value = data
  } catch {
    toast.error('Erreur lors du chargement des promotions')
  } finally {
    loading.value = false
  }
}

function buildPayload() {
  const payload: Record<string, unknown> = {
    code: promoForm.value.code,
    type: promoForm.value.type,
    value: promoForm.value.value,
    min_purchase: promoForm.value.min_purchase || 0,
  }
  if (promoForm.value.starts_at) payload.starts_at = promoForm.value.starts_at
  if (promoForm.value.ends_at) payload.ends_at = promoForm.value.ends_at
  if (promoForm.value.max_uses) payload.max_uses = promoForm.value.max_uses
  return payload
}

async function savePromotion() {
  try {
    if (editingPromo.value) {
      const { data } = await adminApi.promotions.update(editingPromo.value.id, buildPayload())
      const index = promotions.value.findIndex(p => p.id === editingPromo.value!.id)
      if (index !== -1) promotions.value[index] = data
      editingPromo.value = null
      toast.success('Promotion modifiée')
    } else {
      const { data } = await adminApi.promotions.create({ ...buildPayload(), is_active: true })
      promotions.value.push(data)
      toast.success('Promotion créée')
    }
    resetForm()
  } catch {
    toast.error('Erreur lors de l\'enregistrement')
  }
}

function editPromotion(promo: Promotion) {
  editingPromo.value = promo
  promoForm.value = {
    code: promo.code,
    type: promo.type,
    value: promo.value,
    starts_at: toDateInput(promo.starts_at),
    ends_at: toDateInput(promo.ends_at),
    max_uses: promo.max_uses,
    min_purchase: promo.min_purchase ?? 0,
  }
}

function cancelEdit() {
  editingPromo.value = null
  resetForm()
}

async function togglePromoStatus(promo: Promotion) {
  try {
    const { data } = await adminApi.promotions.update(promo.id, { is_active: !promo.is_active })
    const index = promotions.value.findIndex(p => p.id === promo.id)
    if (index !== -1) promotions.value[index] = data
    toast.success(data.is_active ? 'Promotion activée' : 'Promotion désactivée')
  } catch {
    toast.error('Erreur lors de la mise à jour')
  }
}

function deletePromotion(id: number) {
  deleteTarget.value = id
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  try {
    await adminApi.promotions.delete(deleteTarget.value)
    promotions.value = promotions.value.filter(p => p.id !== deleteTarget.value)
    toast.success('Promotion supprimée')
  } catch {
    toast.error('Erreur lors de la suppression')
  } finally {
    deleteTarget.value = null
  }
}

function resetForm() {
  promoForm.value = {
    code: '',
    type: 'percentage',
    value: 0,
    starts_at: todayInput(),
    ends_at: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().slice(0, 10),
    max_uses: undefined,
    min_purchase: 0,
  }
}
</script>
