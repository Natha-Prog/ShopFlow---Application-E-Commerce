<template>
  <AdminLayout>
    <div>
      <!-- Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Gestion des utilisateurs</h1>
          <p class="text-gray-600 mt-1">Gérez les comptes utilisateurs et leurs rôles</p>
        </div>
        <button 
          @click="showModal = true"
          class="bg-gradient-to-r from-yellow-700 to-amber-600 text-white px-6 py-3 rounded-lg hover:from-yellow-800 hover:to-amber-700 transition shadow-lg flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Ajouter un utilisateur
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-600">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Total utilisateurs</p>
              <p class="text-3xl font-bold text-gray-800">{{ users.length }}</p>
            </div>
            <div class="w-12 h-12 bg-gold-pale rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Administrateurs</p>
              <p class="text-3xl font-bold text-gray-800">{{ adminCount }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Clients actifs</p>
              <p class="text-3xl font-bold text-gray-800">{{ activeUsers }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm">Nouveaux (30j)</p>
              <p class="text-3xl font-bold text-gray-800">{{ newUsers }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
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
              placeholder="Rechercher un utilisateur (nom, email...)"
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
            />
          </div>
          <select 
            v-model="roleFilter"
            class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
          >
            <option value="all">Tous les rôles</option>
            <option value="admin">Administrateurs</option>
            <option value="client">Clients</option>
          </select>
        </div>
      </div>
      
      <!-- Users Table -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
        <p class="text-gray-600 mt-4">Chargement...</p>
      </div>
      <div v-else-if="filteredUsers.length === 0" class="bg-white rounded-xl shadow-md p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <p class="text-gray-600 text-lg">Aucun utilisateur trouvé</p>
      </div>
      <div v-else class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Utilisateur</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rôle</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Commandes</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date d'inscription</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-r from-yellow-600 to-amber-600 flex items-center justify-center text-white font-bold">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ user.name }}</p>
                    <p class="text-sm text-gray-500">ID: {{ user.id }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="text-gray-800">{{ user.email }}</p>
              </td>
              <td class="px-6 py-4">
                <span 
                  class="px-3 py-1 rounded-full text-sm font-semibold"
                  :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gold-pale text-yellow-800'"
                >
                  {{ user.role === 'admin' ? 'Administrateur' : 'Client' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <p class="font-semibold text-gray-800">{{ user.order_count || 0 }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="text-gray-800">{{ new Date(user.created_at).toLocaleDateString('fr-FR') }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button 
                    @click="editUser(user)"
                    class="p-2 rounded-lg bg-gold-pale text-gold hover:bg-yellow-100 transition"
                    title="Modifier"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button 
                    @click="toggleRole(user)"
                    class="p-2 rounded-lg bg-purple-100 text-purple-600 hover:bg-purple-200 transition"
                    :title="user.role === 'admin' ? 'Rétrograder' : 'Promouvoir admin'"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                  </button>
                  <button 
                    @click="deleteUser(user.id)"
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
      
      <!-- Add/Edit User Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4">
          <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">{{ editingUser ? 'Modifier' : 'Ajouter' }} un utilisateur</h2>
          </div>
          <form @submit.prevent="saveUser" class="p-6 space-y-4">
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
              <label class="block text-gray-700 mb-2 font-medium">Email</label>
              <input 
                v-model="form.email"
                type="email" 
                required
                :disabled="editingUser !== null"
                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
              />
            </div>
            <div v-if="!editingUser">
              <label class="block text-gray-700 mb-2 font-medium">Mot de passe</label>
              <input 
                v-model="form.password"
                type="password" 
                required
                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
              />
            </div>
            <div>
              <label class="block text-gray-700 mb-2 font-medium">Rôle</label>
              <select 
                v-model="form.role"
                required
                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
              >
                <option value="client">Client</option>
                <option value="admin">Administrateur</option>
              </select>
            </div>
            
            <div class="flex gap-4 pt-4">
              <button 
                type="submit"
                class="flex-1 bg-gradient-to-r from-yellow-700 to-amber-600 text-white py-3 rounded-lg hover:from-yellow-800 hover:to-amber-700 transition shadow-md font-semibold"
              >
                {{ editingUser ? 'Modifier' : 'Créer' }}
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
    </div>

    <!-- Delete confirmation -->
    <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md m-4 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-2">Supprimer l'utilisateur</h2>
        <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
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
const users = ref<any[]>([])
const loading = ref(false)
const showModal = ref(false)
const editingUser = ref<any>(null)
const searchQuery = ref('')
const roleFilter = ref('all')
const deleteTarget = ref<number | null>(null)

const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'client'
})

const adminCount = computed(() => users.value.filter(u => u.role === 'admin').length)
const activeUsers = computed(() => users.value.length)
const newUsers = computed(() => {
  const thirtyDaysAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)
  return users.value.filter(u => new Date(u.created_at) >= thirtyDaysAgo).length
})

const filteredUsers = computed(() => {
  let filtered = users.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(u => 
      u.name.toLowerCase().includes(query) || 
      u.email.toLowerCase().includes(query)
    )
  }
  
  if (roleFilter.value !== 'all') {
    filtered = filtered.filter(u => u.role === roleFilter.value)
  }
  
  return filtered
})

onMounted(async () => {
  await fetchUsers()
})

async function fetchUsers() {
  loading.value = true
  try {
    const { data } = await adminApi.users.list()
    users.value = data
  } catch {
    toast.error('Erreur lors du chargement des utilisateurs')
  } finally {
    loading.value = false
  }
}

function editUser(user: any) {
  editingUser.value = user
  form.value = {
    name: user.name,
    email: user.email,
    password: '',
    role: user.role
  }
  showModal.value = true
}

async function toggleRole(user: any) {
  if (user.id === 1) {
    toast.warning('Impossible de modifier le rôle de l\'administrateur principal')
    return
  }
  const newRole = user.role === 'admin' ? 'client' : 'admin'
  try {
    const { data } = await adminApi.users.update(user.id, { role: newRole })
    const index = users.value.findIndex(u => u.id === user.id)
    if (index !== -1) users.value[index] = data
    toast.success('Rôle mis à jour')
  } catch {
    toast.error('Erreur lors de la mise à jour du rôle')
  }
}

async function saveUser() {
  try {
    if (editingUser.value) {
      const payload: Record<string, unknown> = { name: form.value.name, role: form.value.role }
      if (form.value.password) payload.password = form.value.password
      const { data } = await adminApi.users.update(editingUser.value.id, payload)
      const index = users.value.findIndex(u => u.id === editingUser.value!.id)
      if (index !== -1) users.value[index] = data
      toast.success('Utilisateur modifié')
    } else {
      const { data } = await adminApi.users.create({ ...form.value })
      users.value.push(data)
      toast.success('Utilisateur créé')
    }
    closeModal()
  } catch {
    toast.error('Erreur lors de l\'enregistrement')
  }
}

function deleteUser(id: number) {
  if (id === 1) {
    toast.warning('Impossible de supprimer l\'administrateur principal')
    return
  }
  deleteTarget.value = id
}

async function confirmDelete() {
  if (!deleteTarget.value) return
  try {
    await adminApi.users.delete(deleteTarget.value)
    users.value = users.value.filter(u => u.id !== deleteTarget.value)
    toast.success('Utilisateur supprimé')
  } catch {
    toast.error('Erreur lors de la suppression')
  } finally {
    deleteTarget.value = null
  }
}

function closeModal() {
  showModal.value = false
  editingUser.value = null
  form.value = {
    name: '',
    email: '',
    password: '',
    role: 'client'
  }
}
</script>
