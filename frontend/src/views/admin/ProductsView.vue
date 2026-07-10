<template>
  <AdminLayout>
    <div>
      <!-- Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Gestion des produits</h1>
          <p class="text-gray-600 mt-1">Gérez votre catalogue et votre stock</p>
        </div>
        <button 
          @click="showModal = true"
          class="bg-gradient-to-r from-yellow-700 to-amber-600 text-white px-6 py-3 rounded-lg hover:from-yellow-800 hover:to-amber-700 transition shadow-lg flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Ajouter un produit
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Total produits</p>
              <p class="text-3xl font-bold text-gray-800">{{ products.length }}</p>
            </div>
            <div class="w-12 h-12 bg-gold-pale rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Stock total</p>
              <p class="text-3xl font-bold text-gray-800">{{ totalStock }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Stock faible</p>
              <p class="text-3xl font-bold text-gray-800">{{ lowStockCount }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Rupture de stock</p>
              <p class="text-3xl font-bold text-gray-800">{{ outOfStockCount }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
              placeholder="Rechercher un produit..."
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
            />
          </div>
          <select 
            v-model="stockFilter"
            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
          >
            <option value="all">Tous les stocks</option>
            <option value="low">Stock faible</option>
            <option value="out">Rupture de stock</option>
            <option value="available">En stock</option>
          </select>
        </div>
      </div>
      
      <!-- Products Table -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
        <p class="text-gray-600 mt-4">Chargement...</p>
      </div>
      <div v-else-if="filteredProducts.length === 0" class="bg-white rounded-xl shadow-md p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <p class="text-gray-600 text-lg">Aucun produit trouvé</p>
      </div>
      <div v-else class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catégorie</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prix</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="product in filteredProducts" :key="product.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                  <img 
                    :src="product.image_url" 
                    :alt="product.name"
                    class="w-16 h-16 object-cover rounded-lg shadow-sm"
                  />
                  <div>
                    <p class="font-semibold text-gray-800">{{ product.name }}</p>
                    <p class="text-sm text-gray-500 truncate max-w-xs">{{ product.description }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-gold-pale text-yellow-800">
                  {{ getCategoryName(product.category_id) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <p class="font-semibold text-gray-800">{{ product.price.toFixed(2) }} MGA</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button 
                    @click="adjustStock(product, -1)"
                    class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 transition flex items-center justify-center"
                  >
                    -
                  </button>
                  <span class="font-semibold text-gray-800 w-12 text-center">{{ product.stock }}</span>
                  <button 
                    @click="adjustStock(product, 1)"
                    class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 transition flex items-center justify-center"
                  >
                    +
                  </button>
                </div>
              </td>
              <td class="px-6 py-4">
                <span 
                  class="px-3 py-1 rounded-full text-sm font-semibold"
                  :class="getStockStatusClass(product.stock)"
                >
                  {{ getStockStatus(product.stock) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button 
                    @click="editProduct(product)"
                    class="p-2 rounded-lg bg-gold-pale text-gold hover:bg-yellow-100 transition"
                    title="Modifier"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button 
                    @click="quickStockModal(product)"
                    class="p-2 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 transition"
                    title="Ajuster stock"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                  </button>
                  <button 
                    @click="deleteProduct(product.id)"
                    class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition"
                    title="Supprimer"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Add/Edit Product Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto m-4">
          <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">{{ editingProduct ? 'Modifier' : 'Ajouter' }} un produit</h2>
          </div>
          <form @submit.prevent="saveProduct" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-700 mb-2 font-medium">Nom</label>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                />
              </div>
              <div>
                <label class="block text-gray-700 mb-2 font-medium">Catégorie</label>
                <select 
                  v-model.number="form.category_id"
                  required
                  class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                >
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
            </div>
            
            <div>
              <label class="block text-gray-700 mb-2 font-medium">Description</label>
              <textarea 
                v-model="form.description"
                required
                rows="3"
                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
              ></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-gray-700 mb-2 font-medium">Prix (MGA)</label>
                <input 
                  v-model.number="form.price"
                  type="number" 
                  step="0.01"
                  required
                  class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                />
              </div>
              <div>
                <label class="block text-gray-700 mb-2 font-medium">Stock</label>
                <input 
                  v-model.number="form.stock"
                  type="number" 
                  required
                  class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                />
              </div>
              <div>
                <label class="block text-gray-700 mb-2 font-medium">Marque</label>
                <input 
                  v-model="form.brand"
                  type="text" 
                  class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-gray-700 mb-2 font-medium">Taille</label>
                <input 
                  v-model="form.size"
                  type="text" 
                  class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                />
              </div>
              <div>
                <label class="block text-gray-700 mb-2 font-medium">Couleur</label>
                <input 
                  v-model="form.color"
                  type="text" 
                  class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                />
              </div>
              <div>
                <label class="block text-gray-700 mb-2 font-medium">URL de l'image</label>
                <input 
                  v-model="form.image_url"
                  type="text" 
                  class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                />
              </div>
            </div>
            
            <div class="flex gap-4 pt-4">
              <button 
                type="submit"
                class="flex-1 bg-gradient-to-r from-yellow-700 to-amber-600 text-white py-3 rounded-lg hover:from-yellow-800 hover:to-amber-700 transition shadow-md font-semibold"
              >
                {{ editingProduct ? 'Modifier' : 'Créer' }}
              </button>
              <button 
                type="button"
                @click="closeModal"
                class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold"
              >
                Annuler
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Quick Stock Modal -->
      <div v-if="showStockModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 p-6">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Ajuster le stock</h2>
          <p class="text-gray-600 mb-4">{{ stockProduct?.name }}</p>
          <div class="flex items-center gap-4 mb-6">
            <button 
              @click="stockAdjustment--"
              class="w-12 h-12 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition flex items-center justify-center text-2xl font-bold"
            >
              -
            </button>
            <div class="flex-1 text-center">
              <p class="text-4xl font-bold text-gray-800">{{ stockAdjustment }}</p>
              <p class="text-sm text-gray-500">Nouveau stock: {{ stockProduct?.stock + stockAdjustment }}</p>
            </div>
            <button 
              @click="stockAdjustment++"
              class="w-12 h-12 rounded-full bg-green-100 text-green-600 hover:bg-green-200 transition flex items-center justify-center text-2xl font-bold"
            >
              +
            </button>
          </div>
          <div class="flex gap-4">
            <button 
              @click="confirmStockAdjustment"
              class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold"
            >
              Confirmer
            </button>
            <button 
              @click="closeStockModal"
              class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold"
            >
              Annuler
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete confirmation -->
    <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Supprimer le produit</h2>
        <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce produit ?</p>
        <div class="flex gap-4">
          <button @click="confirmDelete" class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold">Supprimer</button>
          <button @click="deleteTarget = null" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">Annuler</button>
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
const products = ref<any[]>([])
const categories = ref<any[]>([])
const loading = ref(false)
const showModal = ref(false)
const showStockModal = ref(false)
const editingProduct = ref<any>(null)
const stockProduct = ref<any>(null)
const stockAdjustment = ref(0)
const searchQuery = ref('')
const stockFilter = ref('all')
const deleteTarget = ref<number | null>(null)

const form = ref({
  name: '',
  description: '',
  price: 0,
  stock: 0,
  image_url: '',
  category_id: 1,
  brand: '',
  size: '',
  color: ''
})

const totalStock = computed(() => products.value.reduce((acc, p) => acc + p.stock, 0))
const lowStockCount = computed(() => products.value.filter(p => p.stock > 0 && p.stock < 10).length)
const outOfStockCount = computed(() => products.value.filter(p => p.stock === 0).length)

const filteredProducts = computed(() => {
  let filtered = products.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(p => 
      p.name.toLowerCase().includes(query) || 
      p.description.toLowerCase().includes(query) ||
      p.brand?.toLowerCase().includes(query)
    )
  }
  
  if (stockFilter.value === 'low') {
    filtered = filtered.filter(p => p.stock > 0 && p.stock < 10)
  } else if (stockFilter.value === 'out') {
    filtered = filtered.filter(p => p.stock === 0)
  } else if (stockFilter.value === 'available') {
    filtered = filtered.filter(p => p.stock > 0)
  }
  
  return filtered
})

onMounted(async () => {
  await fetchProducts()
  await fetchCategories()
})

async function fetchProducts() {
  loading.value = true
  try {
    const { data } = await adminApi.products.list()
    products.value = data
  } catch {
    toast.error('Erreur lors du chargement des produits')
  } finally {
    loading.value = false
  }
}

async function fetchCategories() {
  try {
    const { data } = await adminApi.categories.list()
    categories.value = data
  } catch {
    toast.error('Erreur lors du chargement des catégories')
  }
}

function getCategoryName(categoryId: number): string {
  const category = categories.value.find(c => c.id === categoryId)
  return category?.name || 'Non classé'
}

function getStockStatus(stock: number): string {
  if (stock === 0) return 'Rupture'
  if (stock < 10) return 'Stock faible'
  return 'En stock'
}

function getStockStatusClass(stock: number): string {
  if (stock === 0) return 'bg-red-100 text-red-800'
  if (stock < 10) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

async function adjustStock(product: any, amount: number) {
  const newStock = Math.max(0, product.stock + amount)
  if (newStock === product.stock) return
  await saveStock(product, newStock)
}

function quickStockModal(product: any) {
  stockProduct.value = product
  stockAdjustment.value = 0
  showStockModal.value = true
}

async function confirmStockAdjustment() {
  if (stockProduct.value) {
    const newStock = Math.max(0, stockProduct.value.stock + stockAdjustment.value)
    await saveStock(stockProduct.value, newStock)
  }
  closeStockModal()
}

function closeStockModal() {
  showStockModal.value = false
  stockProduct.value = null
  stockAdjustment.value = 0
}

async function saveStock(product: any, stock: number) {
  try {
    const { data } = await adminApi.products.update(product.id, { stock })
    const index = products.value.findIndex(p => p.id === product.id)
    if (index !== -1) products.value[index] = data
    toast.success('Stock mis à jour')
  } catch {
    toast.error('Erreur lors de la mise à jour du stock')
    await fetchProducts()
  }
}

function editProduct(product: any) {
  editingProduct.value = product
  form.value = {
    name: product.name,
    description: product.description,
    price: product.price,
    stock: product.stock,
    image_url: product.image_url,
    category_id: product.category_id,
    brand: product.brand || '',
    size: product.size || '',
    color: product.color || ''
  }
  showModal.value = true
}

async function saveProduct() {
  try {
    if (editingProduct.value) {
      const { data } = await adminApi.products.update(editingProduct.value.id, { ...form.value })
      const index = products.value.findIndex(p => p.id === editingProduct.value!.id)
      if (index !== -1) products.value[index] = data
      toast.success('Produit modifié')
    } else {
      const { data } = await adminApi.products.create({ ...form.value })
      products.value.push(data)
      toast.success('Produit créé')
    }
    closeModal()
  } catch {
    toast.error('Erreur lors de l\'enregistrement du produit')
  }
}

function deleteProduct(id: number) {
  deleteTarget.value = id
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  try {
    await adminApi.products.delete(deleteTarget.value)
    products.value = products.value.filter(p => p.id !== deleteTarget.value)
    toast.success('Produit supprimé')
  } catch {
    toast.error('Erreur lors de la suppression')
  } finally {
    deleteTarget.value = null
  }
}

function closeModal() {
  showModal.value = false
  editingProduct.value = null
  form.value = {
    name: '',
    description: '',
    price: 0,
    stock: 0,
    image_url: '',
    category_id: 1,
    brand: '',
    size: '',
    color: ''
  }
}
</script>
