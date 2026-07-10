<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Mon Profil</h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="card">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Informations personnelles</h2>
          <form class="space-y-4" @submit.prevent="updateProfile">
            <AppInput v-model="user.name" label="Nom" type="text" />
            <AppInput v-model="user.email" label="Email" type="email" disabled />
            <AppInput v-model="user.phone" label="Téléphone" type="tel" />
            <AppButton type="submit" :loading="profileLoading" class="w-full">Mettre à jour</AppButton>
          </form>
        </div>

        <div class="card">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Adresses de livraison</h2>

          <div v-if="addressesLoading" class="text-gray-500 text-sm">Chargement...</div>
          <div v-else class="space-y-4 mb-6">
            <div v-for="address in addresses" :key="address.id" class="border rounded-lg p-4 relative">
              <button
                type="button"
                class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                @click="removeAddress(address.id)"
              >
                ×
              </button>
              <p v-if="address.label" class="text-sm text-gray-500 mb-1">{{ address.label }}</p>
              <p class="font-semibold">{{ address.first_name }} {{ address.last_name }}</p>
              <p class="text-gray-600">{{ address.street }}</p>
              <p class="text-gray-600">{{ address.postal_code }} {{ address.city }}</p>
              <p class="text-gray-600">{{ address.country }}</p>
            </div>
            <p v-if="addresses.length === 0" class="text-gray-500 text-sm">Aucune adresse enregistrée</p>
          </div>

          <div class="border-t pt-4">
            <h3 class="font-semibold mb-3">Ajouter une adresse</h3>
            <form class="space-y-3" @submit.prevent="addAddress">
              <AppInput v-model="newAddress.label" label="Libellé (optionnel)" placeholder="Domicile, Bureau..." />
              <div class="grid grid-cols-2 gap-3">
                <AppInput v-model="newAddress.first_name" label="Prénom" required />
                <AppInput v-model="newAddress.last_name" label="Nom" required />
              </div>
              <AppInput v-model="newAddress.street" label="Adresse" required />
              <div class="grid grid-cols-2 gap-3">
                <AppInput v-model="newAddress.city" label="Ville" required />
                <AppInput v-model="newAddress.postal_code" label="Code postal" required />
              </div>
              <AppInput v-model="newAddress.country" label="Pays" required />
              <AppInput v-model="newAddress.phone" label="Téléphone" type="tel" />
              <AppButton type="submit" :loading="addressSaving" class="w-full">Ajouter</AppButton>
            </form>
          </div>
        </div>

        <div class="card">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Favoris</h2>

          <div v-if="favoritesStore.loading" class="text-gray-500 text-sm">Chargement...</div>
          <div v-else-if="favoritesStore.favorites.length === 0" class="text-center py-8 text-gray-600">
            Aucun favori pour le moment
          </div>
          <div v-else class="space-y-4">
            <div
              v-for="product in favoritesStore.favorites"
              :key="product.id"
              class="flex items-center gap-4 border rounded-lg p-3"
            >
              <img
                :src="product.image_url"
                :alt="product.name"
                class="w-16 h-16 object-cover rounded"
              />
              <div class="flex-1 min-w-0">
                <h3 class="font-semibold truncate">{{ product.name }}</h3>
                <p class="text-gold font-bold">{{ formatPrice(product.price) }}</p>
              </div>
              <button
                type="button"
                class="text-red-500 hover:text-red-700 shrink-0"
                @click="removeFavorite(product.id)"
              >
                ♥
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Changer le mot de passe</h2>
        <form class="grid grid-cols-1 md:grid-cols-3 gap-4" @submit.prevent="changePassword">
          <AppInput v-model="password.current" label="Mot de passe actuel" type="password" required />
          <AppInput v-model="password.new" label="Nouveau mot de passe" type="password" required />
          <AppInput v-model="password.confirm" label="Confirmer le mot de passe" type="password" required />
          <div class="md:col-span-3">
            <AppButton type="submit" :loading="passwordLoading">Changer le mot de passe</AppButton>
          </div>
        </form>
      </div>

      <div class="card mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Préférences de notification</h2>
        <div class="space-y-6">
          <div>
            <h3 class="font-semibold text-gray-800 mb-3">Notifications par email</h3>
            <div class="space-y-2 ml-2">
              <label v-for="key in emailKeys" :key="key" class="flex items-center gap-2">
                <input
                  v-model="notificationPreferences.email[key]"
                  type="checkbox"
                  class="w-4 h-4 text-gold rounded"
                  @change="updateNotificationPreference('email', key)"
                />
                <span>{{ emailLabels[key] }}</span>
              </label>
            </div>
          </div>
          <div>
            <h3 class="font-semibold text-gray-800 mb-3">Notifications SMS</h3>
            <div class="space-y-2 ml-2">
              <label v-for="key in smsKeys" :key="key" class="flex items-center gap-2">
                <input
                  v-model="notificationPreferences.sms[key]"
                  type="checkbox"
                  class="w-4 h-4 text-green-600 rounded"
                  @change="updateNotificationPreference('sms', key)"
                />
                <span>{{ smsLabels[key] }}</span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppButton from '@/components/ui/AppButton.vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import { useFavoritesStore } from '@/stores/favorites'
import { useNotifications } from '@/composables/useNotifications'
import { useCurrency } from '@/composables/useCurrency'
import { useToast } from '@/composables/useToast'

interface Address {
  id: number
  label?: string
  first_name: string
  last_name: string
  street: string
  city: string
  postal_code: string
  country: string
  phone?: string
}

const authStore = useAuthStore()
const favoritesStore = useFavoritesStore()
const { notificationPreferences, loadPreferences, updatePreferences } = useNotifications()
const { formatPrice } = useCurrency()
const toast = useToast()

const user = ref({ name: '', email: '', phone: '' })
const addresses = ref<Address[]>([])
const addressesLoading = ref(false)
const addressSaving = ref(false)
const profileLoading = ref(false)
const passwordLoading = ref(false)

const newAddress = ref({
  label: '',
  first_name: '',
  last_name: '',
  street: '',
  city: '',
  postal_code: '',
  country: 'France',
  phone: '',
})

const password = ref({ current: '', new: '', confirm: '' })

const emailKeys = ['orders', 'promotions', 'account', 'newsletter'] as const
const smsKeys = ['orders', 'promotions', 'account'] as const

const emailLabels: Record<string, string> = {
  orders: 'Mises à jour de commandes',
  promotions: 'Promotions et offres',
  account: 'Mises à jour du compte',
  newsletter: 'Newsletter',
}

const smsLabels: Record<string, string> = {
  orders: 'Mises à jour de commandes',
  promotions: 'Promotions et offres',
  account: 'Mises à jour du compte',
}

onMounted(async () => {
  await authStore.fetchUser()
  if (authStore.user) {
    user.value = {
      name: authStore.user.name || '',
      email: authStore.user.email || '',
      phone: authStore.user.phone || '',
    }
  }
  await Promise.all([loadAddresses(), favoritesStore.fetchFavorites(), loadPreferences()])
})

async function loadAddresses() {
  addressesLoading.value = true
  try {
    const response = await api.get('/addresses')
    addresses.value = response.data
  } catch {
    addresses.value = []
  } finally {
    addressesLoading.value = false
  }
}

async function addAddress() {
  addressSaving.value = true
  try {
    const response = await api.post('/addresses', newAddress.value)
    addresses.value.push(response.data)
    newAddress.value = {
      label: '',
      first_name: '',
      last_name: '',
      street: '',
      city: '',
      postal_code: '',
      country: 'France',
      phone: '',
    }
    toast.success('Adresse ajoutée')
  } catch {
    toast.error('Erreur lors de l\'ajout de l\'adresse')
  } finally {
    addressSaving.value = false
  }
}

async function removeAddress(id: number) {
  try {
    await api.delete(`/addresses/${id}`)
    addresses.value = addresses.value.filter((a) => a.id !== id)
    toast.success('Adresse supprimée')
  } catch {
    toast.error('Erreur lors de la suppression')
  }
}

async function removeFavorite(productId: number) {
  try {
    await favoritesStore.removeFavorite(productId)
    toast.info('Retiré des favoris')
  } catch {
    toast.error('Erreur lors de la suppression du favori')
  }
}

async function updateProfile() {
  profileLoading.value = true
  try {
    const response = await api.put('/profile', {
      name: user.value.name,
      phone: user.value.phone || null,
    })
    authStore.user = response.data
    toast.success('Profil mis à jour')
  } catch {
    toast.error('Erreur lors de la mise à jour du profil')
  } finally {
    profileLoading.value = false
  }
}

async function changePassword() {
  if (password.value.new !== password.value.confirm) {
    toast.error('Les mots de passe ne correspondent pas')
    return
  }
  passwordLoading.value = true
  try {
    await api.put('/profile/password', {
      current_password: password.value.current,
      password: password.value.new,
      password_confirmation: password.value.confirm,
    })
    toast.success('Mot de passe changé')
    password.value = { current: '', new: '', confirm: '' }
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Erreur lors du changement de mot de passe')
  } finally {
    passwordLoading.value = false
  }
}

function updateNotificationPreference(type: 'email' | 'sms', category: string) {
  const prefs = notificationPreferences.value[type] as Record<string, boolean>
  updatePreferences(type, category, prefs[category] ?? false)
}
</script>
