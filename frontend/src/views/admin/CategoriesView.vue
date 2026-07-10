<template>
  <AdminLayout>
    <div>
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Gestion des catégories</h1>
          <p class="text-gray-600 mt-1">Organisez votre catalogue par catégories</p>
        </div>
        <button 
          @click="openCreate"
          class="bg-gradient-to-r from-yellow-700 to-amber-600 text-white px-6 py-3 rounded-lg hover:from-yellow-800 hover:to-amber-700 transition shadow-lg flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Ajouter une catégorie
        </button>
      </div>

      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
        <p class="text-gray-600 mt-4">Chargement...</p>
      </div>
      <div v-else-if="categories.length === 0" class="bg-white rounded-xl shadow-md p-12 text-center">
        <p class="text-gray-600 text-lg">Aucune catégorie</p>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="category in categories" 
          :key="category.id"
          class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition"
        >
          <div class="h-32 bg-gradient-to-r from-yellow-600 to-amber-600 flex items-center justify-center">
            <img v-if="category.image" :src="category.image" :alt="category.name" class="w-full h-full object-cover" />
            <svg v-else class="w-16 h-16 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
          </div>
          <div class="p-4">
            <h3 class="text-lg font-bold text-gray-800">{{ category.name }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ category.products_count ?? 0 }} produit(s)</p>
            <div class="flex gap-2 mt-4">
              <button 
                @click="editCategory(category)"
                class="flex-1 p-2 rounded-lg bg-gold-pale text-gold hover:bg-yellow-100 transition text-sm font-medium"
              >
                Modifier
              </button>
              <button 
                @click="deleteCategory(category.id)"
                class="flex-1 p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition text-sm font-medium"
              >
                Supprimer
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Add/Edit Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4">
          <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">{{ editingCategory ? 'Modifier' : 'Ajouter' }} une catégorie</h2>
          </div>
          <form @submit.prevent="saveCategory" class="p-6 space-y-4">
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
              <label class="block text-gray-700 mb-2 font-medium">URL de l'image</label>
              <input 
                v-model="form.image"
                type="text" 
                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
                placeholder="https://..."
              />
            </div>
            <div class="flex gap-4 pt-4">
              <button type="submit" class="flex-1 bg-gradient-to-r from-yellow-700 to-amber-600 text-white py-3 rounded-lg hover:from-yellow-800 hover:to-amber-700 transition font-semibold">
                {{ editingCategory ? 'Modifier' : 'Créer' }}
              </button>
              <button type="button" @click="closeModal" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                Annuler
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete confirmation -->
      <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-2">Supprimer la catégorie</h2>
          <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
          <div class="flex gap-4">
            <button @click="confirmDelete" class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold">Supprimer</button>
            <button @click="deleteTarget = null" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">Annuler</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { adminApi } from '@/services/admin'
import { useToast } from '@/composables/useToast'

interface Category {
  id: number
  name: string
  slug: string
  image?: string
  products_count?: number
}

const toast = useToast()
const categories = ref<Category[]>([])
const loading = ref(false)
const showModal = ref(false)
const editingCategory = ref<Category | null>(null)
const deleteTarget = ref<number | null>(null)

const form = ref({
  name: '',
  image: '',
})

onMounted(() => fetchCategories())

async function fetchCategories() {
  loading.value = true
  try {
    const { data } = await adminApi.categories.list()
    categories.value = data
  } catch {
    toast.error('Erreur lors du chargement des catégories')
  } finally {
    loading.value = false
  }
}

function openCreate() {
  editingCategory.value = null
  form.value = { name: '', image: '' }
  showModal.value = true
}

function editCategory(category: Category) {
  editingCategory.value = category
  form.value = { name: category.name, image: category.image || '' }
  showModal.value = true
}

async function saveCategory() {
  try {
    if (editingCategory.value) {
      const { data } = await adminApi.categories.update(editingCategory.value.id, { ...form.value })
      const index = categories.value.findIndex(c => c.id === editingCategory.value!.id)
      if (index !== -1) categories.value[index] = data
      toast.success('Catégorie modifiée')
    } else {
      const { data } = await adminApi.categories.create({ ...form.value })
      categories.value.push(data)
      toast.success('Catégorie créée')
    }
    closeModal()
  } catch {
    toast.error('Erreur lors de l\'enregistrement')
  }
}

function deleteCategory(id: number) {
  deleteTarget.value = id
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  try {
    await adminApi.categories.delete(deleteTarget.value)
    categories.value = categories.value.filter(c => c.id !== deleteTarget.value)
    toast.success('Catégorie supprimée')
  } catch {
    toast.error('Erreur lors de la suppression')
  } finally {
    deleteTarget.value = null
  }
}

function closeModal() {
  showModal.value = false
  editingCategory.value = null
  form.value = { name: '', image: '' }
}
</script>
