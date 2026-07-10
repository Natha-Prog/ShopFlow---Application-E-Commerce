<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Commander</h1>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-6">
          <div class="card">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Adresse de livraison</h2>

            <div v-if="savedAddresses.length > 0" class="mb-4 space-y-2">
              <label class="block text-sm font-medium text-gray-700">Adresses enregistrées</label>
              <select v-model="selectedAddressId" class="input-field" @change="fillFromSavedAddress">
                <option :value="null">Nouvelle adresse</option>
                <option v-for="addr in savedAddresses" :key="addr.id" :value="addr.id">
                  {{ addr.label || `${addr.first_name} ${addr.last_name}` }} — {{ addr.street }}, {{ addr.city }}
                </option>
              </select>
            </div>

            <form class="space-y-4" @submit.prevent="handleCheckout">
              <div class="grid grid-cols-2 gap-4">
                <AppInput v-model="shippingAddress.first_name" label="Prénom" required />
                <AppInput v-model="shippingAddress.last_name" label="Nom" required />
              </div>
              <AppInput v-model="shippingAddress.street" label="Adresse" required />
              <div class="grid grid-cols-2 gap-4">
                <AppInput v-model="shippingAddress.city" label="Ville" required />
                <AppInput v-model="shippingAddress.postal_code" label="Code postal" required />
              </div>
              <AppInput v-model="shippingAddress.country" label="Pays" required />
              <AppInput v-model="shippingAddress.phone" label="Téléphone" type="tel" />
            </form>
          </div>

          <div class="card">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Mode de livraison</h2>
            <div class="space-y-3">
              <div
                v-for="option in shippingOptions"
                :key="option.id"
                class="border rounded-lg transition overflow-hidden"
                :class="selectedShipping === option.id ? 'border-yellow-600 bg-gold-pale' : 'hover:border-yellow-600'"
              >
                <label class="flex items-center justify-between p-4 cursor-pointer">
                  <div class="flex items-center gap-3">
                    <input v-model="selectedShipping" type="radio" :value="option.id" class="w-4 h-4 text-gold" />
                    <div>
                      <p class="font-semibold">{{ option.name }}</p>
                      <p class="text-sm text-gray-600">{{ option.description }}</p>
                    </div>
                  </div>
                  <span class="font-bold text-gold shrink-0 ml-2">
                    {{ option.dynamic ? (quoteLoading && selectedShipping === option.id ? '...' : formatPrice(displayFeeForOption(option.id))) : formatPrice(option.price) }}
                  </span>
                </label>

                <!-- Livraison à domicile : GPS Antananarivo -->
                <div v-if="option.id === 'delivery' && selectedShipping === 'delivery'" class="px-4 pb-4 border-t border-yellow-200/50 pt-4">
                  <p class="text-sm text-gray-600 mb-3">Utilisez votre position pour calculer les frais de livraison (Antananarivo uniquement).</p>
                  <button
                    type="button"
                    class="btn-secondary w-full flex items-center justify-center gap-2 text-sm"
                    :disabled="geoLoading"
                    @click="useMyLocation"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ geoLoading ? 'Localisation en cours...' : 'Utiliser ma position' }}
                  </button>
                  <div v-if="shippingAddress.latitude && shippingAddress.longitude" class="mt-3 p-3 bg-white rounded-lg border text-sm">
                    <p class="text-gray-700">
                      <span class="font-medium">Position :</span>
                      {{ shippingAddress.latitude.toFixed(5) }}, {{ shippingAddress.longitude.toFixed(5) }}
                    </p>
                    <p v-if="shippingQuote?.distance_km" class="text-gray-600 mt-1">
                      Distance : {{ shippingQuote.distance_km }} km
                    </p>
                  </div>
                  <p v-if="geoError" class="mt-2 text-sm text-red-600">{{ geoError }}</p>
                </div>

                <!-- Expédition colis : région Madagascar -->
                <div v-if="option.id === 'shipping' && selectedShipping === 'shipping'" class="px-4 pb-4 border-t border-yellow-200/50 pt-4 space-y-3">
                  <label class="block text-sm font-medium text-gray-700">Région de destination</label>
                  <select v-model="selectedRegion" class="input-field" @change="onRegionChange">
                    <option value="">Sélectionnez une région</option>
                    <option v-for="region in regions" :key="region.code" :value="region.code">
                      {{ region.name }} — {{ formatPrice(region.fee) }}
                    </option>
                  </select>
                  <p class="text-xs text-gray-500">Expédition colis pour les régions hors Antananarivo.</p>
                </div>
              </div>
            </div>

            <div v-if="shippingQuote && selectedShipping !== 'pickup'" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
              {{ shippingQuote.message }} — <strong>{{ formatPrice(shippingQuote.fee) }}</strong>
            </div>
            <p v-if="quoteError" class="mt-3 text-sm text-red-600">{{ quoteError }}</p>
          </div>

          <div class="card">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Mode de paiement</h2>
            <div v-if="paymentMethodsLoading" class="space-y-3">
              <div v-for="n in 3" :key="n" class="skeleton h-16 w-full rounded-lg" />
            </div>
            <div v-else class="space-y-3">
              <div
                v-for="method in paymentMethods"
                :key="method.id"
                class="border rounded-lg transition overflow-hidden"
                :class="selectedPayment === method.id ? 'border-yellow-600 bg-gold-pale' : 'hover:border-yellow-600'"
              >
                <label class="flex items-center gap-3 p-4 cursor-pointer">
                  <input v-model="selectedPayment" type="radio" :value="method.id" class="w-4 h-4 text-gold shrink-0" />
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold">{{ method.name }}</p>
                    <p class="text-sm text-gray-600">{{ method.description }}</p>
                  </div>
                  <span
                    class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-bold shrink-0"
                    :class="paymentIconClass(method.id)"
                  >
                    {{ paymentIconLabel(method.id) }}
                  </span>
                </label>

                <div
                  v-if="selectedPayment === method.id && method.instructions"
                  class="px-4 pb-4 border-t border-yellow-200/50 pt-4 space-y-3"
                >
                  <p class="text-sm text-gray-700">{{ method.instructions }}</p>

                  <div v-if="method.merchant_number" class="p-3 bg-white rounded-lg border">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Numéro marchand</p>
                    <p class="text-lg font-bold text-gold">{{ method.merchant_number }}</p>
                    <p v-if="method.merchant_name" class="text-sm text-gray-600">{{ method.merchant_name }}</p>
                  </div>

                  <div v-if="method.bank_name" class="p-3 bg-white rounded-lg border space-y-1 text-sm">
                    <p><span class="text-gray-500">Banque :</span> <strong>{{ method.bank_name }}</strong></p>
                    <p v-if="method.account_number"><span class="text-gray-500">Compte :</span> <strong>{{ method.account_number }}</strong></p>
                    <p v-if="method.account_holder"><span class="text-gray-500">Titulaire :</span> {{ method.account_holder }}</p>
                  </div>

                  <div v-if="method.requires_phone">
                    <AppInput
                      v-model="paymentPhone"
                      label="Votre numéro Mobile Money"
                      type="tel"
                      placeholder="034 12 345 67"
                      required
                    />
                    <p class="text-xs text-gray-500 mt-1">Préfixes acceptés : {{ method.prefixes.join(', ') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Code promo</h2>
            <div class="flex gap-2">
              <input
                v-model="promoCode"
                type="text"
                placeholder="Entrez votre code promo"
                class="input-field flex-1"
              />
              <AppButton :loading="promoLoading" @click="applyPromoCode">Appliquer</AppButton>
            </div>
            <div v-if="appliedPromo" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
              <p class="text-green-700 font-semibold">{{ appliedPromo.code }} appliqué</p>
              <p class="text-sm text-green-600">Remise de {{ formatPrice(appliedPromo.discount) }}</p>
            </div>
          </div>

          <AppButton
            class="w-full"
            :loading="loading"
            :disabled="!canCheckout"
            @click="handleCheckout"
          >
            Confirmer la commande — {{ formatPrice(totalWithShipping) }}
          </AppButton>
        </div>

        <div class="card h-fit">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Résumé de la commande</h2>
          <div class="space-y-4 mb-6">
            <div v-for="item in cartStore.items" :key="item.id" class="flex justify-between">
              <span>{{ item.product.name }} x{{ item.quantity }}</span>
              <span>{{ formatPrice(item.product.price * item.quantity) }}</span>
            </div>
          </div>
          <hr class="mb-4">
          <div class="space-y-2">
            <div class="flex justify-between">
              <span>Sous-total</span>
              <span>{{ formatPrice(cartStore.total) }}</span>
            </div>
            <div v-if="appliedPromo" class="flex justify-between text-green-600">
              <span>Remise ({{ appliedPromo.code }})</span>
              <span>-{{ formatPrice(appliedPromo.discount) }}</span>
            </div>
            <div class="flex justify-between">
              <span>Livraison</span>
              <span>{{ formatPrice(shippingCost) }}</span>
            </div>
            <p v-if="shippingQuote?.message && selectedShipping !== 'pickup'" class="text-xs text-gray-500">
              {{ shippingQuote.message }}
            </p>
            <hr class="my-2">
            <div class="flex justify-between text-xl font-bold">
              <span>Total</span>
              <span class="text-gold">{{ formatPrice(totalWithShipping) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppButton from '@/components/ui/AppButton.vue'
import api from '@/services/api'
import { getRegions, quoteShipping, type ShippingQuote, type ShippingRegion } from '@/services/shipping'
import { getPaymentMethods, type PaymentMethod } from '@/services/payments'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useCurrency } from '@/composables/useCurrency'
import { useToast } from '@/composables/useToast'

interface SavedAddress {
  id: number
  label?: string
  first_name: string
  last_name: string
  street: string
  city: string
  postal_code: string
  country: string
  phone?: string
  is_default?: boolean
}

interface AppliedPromo {
  code: string
  type: string
  value: number
  discount: number
}

interface ShippingAddress {
  first_name: string
  last_name: string
  street: string
  city: string
  postal_code: string
  country: string
  phone: string
  latitude?: number
  longitude?: number
  region?: string
  distance_km?: number
}

const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()
const { formatPrice } = useCurrency()
const toast = useToast()

const shippingAddress = ref<ShippingAddress>({
  first_name: '',
  last_name: '',
  street: '',
  city: 'Antananarivo',
  postal_code: '',
  country: 'Madagascar',
  phone: '',
})

const savedAddresses = ref<SavedAddress[]>([])
const selectedAddressId = ref<number | null>(null)
const loading = ref(false)
const promoLoading = ref(false)
const quoteLoading = ref(false)
const geoLoading = ref(false)
const selectedShipping = ref<string | null>(null)
const selectedPayment = ref<string | null>(null)
const promoCode = ref('')
const appliedPromo = ref<AppliedPromo | null>(null)
const calculatedShippingCost = ref(0)
const shippingQuote = ref<ShippingQuote | null>(null)
const quoteError = ref('')
const geoError = ref('')
const regions = ref<ShippingRegion[]>([])
const selectedRegion = ref('')
const paymentMethods = ref<PaymentMethod[]>([])
const paymentMethodsLoading = ref(true)
const paymentPhone = ref('')

const shippingOptions = [
  { id: 'pickup', name: 'À récupérer à la boutique', description: 'Gratuit — Récupérez votre commande à notre boutique', price: 0 },
  { id: 'delivery', name: 'Livraison à domicile', description: 'Antananarivo uniquement — frais selon la distance', price: 0, dynamic: true },
  { id: 'shipping', name: 'Expédition colis', description: 'Autres régions de Madagascar — tarif fixe par région', price: 0, dynamic: true },
]

const selectedPaymentMethod = computed(() =>
  paymentMethods.value.find((m) => m.id === selectedPayment.value) ?? null,
)

const shippingCost = computed(() => {
  if (selectedShipping.value === 'pickup') return 0
  return calculatedShippingCost.value
})

const totalWithShipping = computed(() => {
  const discount = appliedPromo.value?.discount ?? 0
  return Math.max(0, cartStore.total - discount + shippingCost.value)
})

const canCheckout = computed(() => {
  if (!selectedShipping.value || !selectedPayment.value) return false

  const method = selectedPaymentMethod.value
  if (method?.requires_phone) {
    const phone = paymentPhone.value.trim() || shippingAddress.value.phone.trim()
    if (!phone) return false
  }

  if (selectedShipping.value === 'pickup') return true
  if (quoteLoading.value) return false
  if (selectedShipping.value === 'delivery') {
    return !!(shippingAddress.value.latitude && shippingAddress.value.longitude && shippingQuote.value)
  }
  if (selectedShipping.value === 'shipping') {
    return !!(selectedRegion.value && shippingQuote.value)
  }
  return false
})

function paymentIconClass(id: string) {
  const classes: Record<string, string> = {
    mvola: 'bg-yellow-100 text-yellow-800',
    orange_money: 'bg-orange-100 text-orange-800',
    airtel_money: 'bg-red-100 text-red-800',
    cash_on_delivery: 'bg-green-100 text-green-800',
    bank_transfer: 'bg-blue-100 text-blue-800',
  }
  return classes[id] ?? 'bg-gray-100 text-gray-700'
}

function paymentIconLabel(id: string) {
  const labels: Record<string, string> = {
    mvola: 'MV',
    orange_money: 'OM',
    airtel_money: 'AM',
    cash_on_delivery: 'MGA',
    bank_transfer: 'BNI',
  }
  return labels[id] ?? 'P'
}

function displayFeeForOption(optionId: string) {
  if (selectedShipping.value !== optionId) return 0
  return calculatedShippingCost.value
}

onMounted(async () => {
  await cartStore.fetchCart()
  await Promise.all([loadAddresses(), loadRegions(), loadPaymentMethods()])
  prefillUserName()
})

watch(selectedPayment, () => {
  paymentPhone.value = shippingAddress.value.phone || ''
})

async function loadPaymentMethods() {
  paymentMethodsLoading.value = true
  try {
    const { methods } = await getPaymentMethods()
    paymentMethods.value = methods
    if (methods.length > 0 && !selectedPayment.value) {
      selectedPayment.value = methods[0]!.id
    }
  } catch {
    paymentMethods.value = []
    toast.error('Impossible de charger les modes de paiement')
  } finally {
    paymentMethodsLoading.value = false
  }
}

watch(selectedShipping, async (newOption) => {
  quoteError.value = ''
  geoError.value = ''
  shippingQuote.value = null
  calculatedShippingCost.value = 0

  if (newOption === 'pickup') {
    await fetchQuote('pickup')
  } else if (newOption === 'delivery') {
    if (shippingAddress.value.latitude && shippingAddress.value.longitude) {
      await fetchQuote('delivery')
    }
  } else if (newOption === 'shipping' && selectedRegion.value) {
    await fetchQuote('shipping')
  }
})

async function loadRegions() {
  try {
    regions.value = await getRegions()
  } catch {
    regions.value = []
  }
}

async function fetchQuote(method: 'pickup' | 'delivery' | 'shipping') {
  quoteLoading.value = true
  quoteError.value = ''
  try {
    const params: Parameters<typeof quoteShipping>[0] = { shipping_method: method }

    if (method === 'delivery') {
      params.latitude = shippingAddress.value.latitude
      params.longitude = shippingAddress.value.longitude
    } else if (method === 'shipping') {
      params.region = selectedRegion.value
    }

    const quote = await quoteShipping(params)
    shippingQuote.value = quote
    calculatedShippingCost.value = quote.fee
  } catch (error: any) {
    shippingQuote.value = null
    calculatedShippingCost.value = 0
    quoteError.value = error.response?.data?.message || 'Impossible de calculer les frais de livraison'
  } finally {
    quoteLoading.value = false
  }
}

function useMyLocation() {
  if (!navigator.geolocation) {
    geoError.value = 'La géolocalisation n\'est pas supportée par votre navigateur'
    return
  }

  geoLoading.value = true
  geoError.value = ''
  quoteError.value = ''

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      shippingAddress.value.latitude = position.coords.latitude
      shippingAddress.value.longitude = position.coords.longitude
      shippingAddress.value.city = 'Antananarivo'
      shippingAddress.value.country = 'Madagascar'
      geoLoading.value = false
      await fetchQuote('delivery')
    },
    (error) => {
      geoLoading.value = false
      geoError.value = error.code === error.PERMISSION_DENIED
        ? 'Permission de localisation refusée. Autorisez l\'accès à votre position.'
        : 'Impossible d\'obtenir votre position. Réessayez.'
    },
    { enableHighAccuracy: true, timeout: 15000 },
  )
}

async function onRegionChange() {
  quoteError.value = ''
  if (!selectedRegion.value) {
    shippingQuote.value = null
    calculatedShippingCost.value = 0
    return
  }
  const region = regions.value.find((r) => r.code === selectedRegion.value)
  if (region) {
    shippingAddress.value.region = region.name
  }
  await fetchQuote('shipping')
}

function prefillUserName() {
  if (!authStore.user) return
  const parts = authStore.user.name.trim().split(/\s+/)
  shippingAddress.value.first_name = parts[0] || ''
  shippingAddress.value.last_name = parts.slice(1).join(' ') || parts[0] || ''
}

async function loadAddresses() {
  try {
    const response = await api.get('/addresses')
    savedAddresses.value = response.data
    const defaultAddr = savedAddresses.value.find((a) => a.is_default) || savedAddresses.value[0]
    if (defaultAddr) {
      selectedAddressId.value = defaultAddr.id
      fillFromSavedAddress()
    }
  } catch {
    savedAddresses.value = []
  }
}

function fillFromSavedAddress() {
  if (!selectedAddressId.value) return
  const addr = savedAddresses.value.find((a) => a.id === selectedAddressId.value)
  if (!addr) return
  shippingAddress.value = {
    ...shippingAddress.value,
    first_name: addr.first_name,
    last_name: addr.last_name,
    street: addr.street,
    city: addr.city,
    postal_code: addr.postal_code,
    country: addr.country,
    phone: addr.phone || '',
  }
}

async function applyPromoCode() {
  if (!promoCode.value.trim()) {
    toast.warning('Veuillez entrer un code promo')
    return
  }
  promoLoading.value = true
  try {
    const response = await api.post('/promotions/validate', {
      code: promoCode.value.trim(),
      subtotal: cartStore.total,
    })
    appliedPromo.value = response.data
    toast.success('Code promo appliqué')
    promoCode.value = ''
  } catch (error: any) {
    appliedPromo.value = null
    toast.error(error.response?.data?.message || 'Code promo invalide')
  } finally {
    promoLoading.value = false
  }
}

async function handleCheckout() {
  if (!canCheckout.value) {
    toast.warning('Veuillez compléter les informations de livraison')
    return
  }

  const { first_name, last_name, street, city, postal_code, country } = shippingAddress.value
  if (!first_name || !last_name || !street || !city || !postal_code || !country) {
    toast.warning('Veuillez remplir l\'adresse de livraison')
    return
  }

  // For Mobile Money, validate phone number
  if (selectedPaymentMethod.value?.requires_phone) {
    const phone = paymentPhone.value.trim() || shippingAddress.value.phone
    if (!phone) {
      toast.warning('Veuillez entrer votre numéro Mobile Money')
      return
    }
  }

  loading.value = true
  try {
    const addressPayload: ShippingAddress = { ...shippingAddress.value }

    if (selectedShipping.value === 'delivery') {
      addressPayload.distance_km = shippingQuote.value?.distance_km ?? undefined
      addressPayload.region = shippingQuote.value?.region ?? 'Analamanga'
    }

    if (selectedShipping.value === 'shipping') {
      addressPayload.region = shippingQuote.value?.region ?? selectedRegion.value
      delete addressPayload.latitude
      delete addressPayload.longitude
    }

    if (selectedShipping.value === 'pickup') {
      delete addressPayload.latitude
      delete addressPayload.longitude
      delete addressPayload.region
      delete addressPayload.distance_km
    }

    const response = await api.post('/orders', {
      shipping_address: addressPayload,
      payment_method: selectedPayment.value,
      payment_phone: selectedPaymentMethod.value?.requires_phone
        ? (paymentPhone.value.trim() || shippingAddress.value.phone)
        : null,
      shipping_method: selectedShipping.value,
      promo_code: appliedPromo.value?.code || null,
    })
    await cartStore.fetchCart()
    toast.success('Commande confirmée')
    router.push(`/orders/${response.data.id}/confirmation`)
  } catch (error: any) {
    console.error('Checkout error:', error)
    toast.error(error.response?.data?.message || 'Erreur lors de la commande')
  } finally {
    loading.value = false
  }
}
</script>
