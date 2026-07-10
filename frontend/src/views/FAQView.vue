<template>
  <div class="min-h-screen bg-gray-50">
    <Header />
    
    <main class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Questions Fréquemment Posées</h1>
      
      <!-- Search -->
      <div class="mb-8">
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Rechercher une question..."
          class="w-full max-w-2xl px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600"
        />
      </div>

      <!-- FAQ Categories -->
      <div class="flex flex-wrap gap-2 mb-8">
        <button 
          v-for="category in categories" 
          :key="category.id"
          @click="selectedCategory = category.id"
          class="px-4 py-2 rounded-full transition"
          :class="selectedCategory === category.id ? 'bg-yellow-700 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
        >
          {{ category.name }}
        </button>
      </div>

      <!-- FAQ Items -->
      <div class="space-y-4">
        <div 
          v-for="faq in filteredFAQs" 
          :key="faq.id"
          class="bg-white rounded-lg shadow overflow-hidden"
        >
          <button 
            @click="toggleFAQ(faq.id)"
            class="w-full px-6 py-4 flex justify-between items-center text-left hover:bg-gray-50 transition"
          >
            <span class="font-semibold text-gray-800">{{ faq.question }}</span>
            <svg 
              class="w-5 h-5 text-gray-500 transition-transform"
              :class="expandedFAQ === faq.id ? 'rotate-180' : ''"
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div 
            v-if="expandedFAQ === faq.id"
            class="px-6 py-4 border-t bg-gray-50"
          >
            <p class="text-gray-600">{{ faq.answer }}</p>
          </div>
        </div>
      </div>

      <!-- No Results -->
      <div v-if="filteredFAQs.length === 0" class="text-center py-12">
        <p class="text-gray-600">{{ t.faq.noResults }}</p>
      </div>

      <!-- Contact CTA -->
      <div class="mt-12 bg-yellow-700 rounded-lg shadow p-8 text-center">
        <h2 class="text-2xl font-bold text-white mb-4">{{ t.faq.notFound }}</h2>
        <p class="text-yellow-100 mb-6">Notre équipe est là pour vous aider!</p>
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mb-6">
          <a href="tel:+261346623495" class="flex items-center gap-2 text-white hover:text-yellow-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            +261 34 66 234 95
          </a>
          <a href="mailto:goldenshopmdg@gmail.com" class="flex items-center gap-2 text-white hover:text-yellow-200 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            goldenshopmdg@gmail.com
          </a>
        </div>
        <router-link to="/contact" class="inline-block bg-white text-gold px-6 py-3 rounded-lg hover:bg-gray-100 transition font-semibold">
          {{ t.faq.contactUs }}
        </router-link>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import Header from '@/components/Header.vue'
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()
const searchQuery = ref('')
const selectedCategory = ref('all')
const expandedFAQ = ref<number | null>(null)

const categories = computed(() => [
  { id: 'all', name: 'Toutes' },
  { id: 'orders', name: 'Commandes' },
  { id: 'shipping', name: 'Livraison' },
  { id: 'payment', name: 'Paiement' },
  { id: 'returns', name: 'Retours' },
  { id: 'account', name: 'Compte' }
])

const faqs = ref([
  {
    id: 1,
    category: 'orders',
    question: 'Comment passer une commande?',
    answer: 'Pour passer une commande, ajoutez les produits souhaités à votre panier, puis cliquez sur le bouton "Panier" en haut à droite. Suivez les instructions pour entrer vos informations de livraison et de paiement, puis confirmez votre commande.'
  },
  {
    id: 2,
    category: 'orders',
    question: 'Puis-je modifier ma commande après l\'avoir validée?',
    answer: 'Une fois la commande validée, vous ne pouvez plus la modifier directement. Cependant, vous pouvez nous contacter immédiatement par email ou téléphone, et nous ferons notre possible pour apporter les changements demandés avant l\'expédition.'
  },
  {
    id: 3,
    category: 'shipping',
    question: 'Quels sont les délais de livraison?',
    answer: 'Les délais de livraison varient selon le mode choisi: Livraison standard (3-5 jours ouvrés), Livraison express (24-48 heures), Point relais (3-5 jours ouvrés). La livraison est gratuite pour toute commande supérieure à 50 000 MGA.'
  },
  {
    id: 4,
    category: 'shipping',
    question: 'Comment suivre ma commande?',
    answer: 'Vous pouvez suivre votre commande depuis votre compte dans la section "Mes commandes". Vous y trouverez un statut en temps réel de votre commande ainsi que les informations de suivi si disponible.'
  },
  {
    id: 5,
    category: 'payment',
    question: 'Quels modes de paiement acceptez-vous?',
    answer: 'Nous acceptons plusieurs modes de paiement: Carte bancaire (Visa, Mastercard), Mobile Money (Orange Money, M-Pesa), Virement bancaire, et Paiement à la livraison (espèces ou carte).'
  },
  {
    id: 6,
    category: 'payment',
    question: 'Le paiement est-il sécurisé?',
    answer: 'Oui, tous les paiements sont entièrement sécurisés grâce à notre système de cryptage SSL. Nous ne stockons jamais vos informations bancaires sur nos serveurs.'
  },
  {
    id: 7,
    category: 'returns',
    question: 'Quelle est votre politique de retour?',
    answer: 'Vous avez 30 jours pour retourner un produit si vous n\'êtes pas satisfait. Le produit doit être dans son état d\'origine, non utilisé et avec tous les étiquettes. Contactez notre service client pour initier un retour.'
  },
  {
    id: 8,
    category: 'returns',
    question: 'Comment obtenir un remboursement?',
    answer: 'Une fois votre retour reçu et vérifié, le remboursement sera effectué sur le même moyen de paiement utilisé pour la commande dans un délai de 5 à 10 jours ouvrés.'
  },
  {
    id: 9,
    category: 'account',
    question: 'Comment créer un compte?',
    answer: 'Cliquez sur le bouton "Inscription" en haut à droite de la page. Remplissez le formulaire avec vos informations personnelles, puis validez. Vous recevrez un email de confirmation.'
  },
  {
    id: 10,
    category: 'account',
    question: 'Puis-je modifier mes informations personnelles?',
    answer: 'Oui, vous pouvez modifier vos informations personnelles depuis votre compte dans la section "Mon Profil". Vous pouvez y changer votre nom, ajouter des adresses de livraison, et gérer vos favoris.'
  },
  {
    id: 11,
    category: 'orders',
    question: 'Comment utiliser un code promo?',
    answer: 'Ajoutez les produits à votre panier et allez à la page de commande. Dans la section "Code promo", entrez votre code et cliquez sur "Appliquer". La réduction sera automatiquement déduite de votre total.'
  },
  {
    id: 12,
    category: 'shipping',
    question: 'Livrez-vous à l\'international?',
    answer: 'Actuellement, nous livrons uniquement en France métropolitaine. Nous prévoyons d\'étendre notre service de livraison à d\'autres pays dans le futur.'
  }
])

const filteredFAQs = computed(() => {
  return faqs.value.filter(faq => {
    const matchesCategory = selectedCategory.value === 'all' || faq.category === selectedCategory.value
    const matchesSearch = faq.question.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          faq.answer.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchesCategory && matchesSearch
  })
})

function toggleFAQ(id: number) {
  expandedFAQ.value = expandedFAQ.value === id ? null : id
}
</script>
