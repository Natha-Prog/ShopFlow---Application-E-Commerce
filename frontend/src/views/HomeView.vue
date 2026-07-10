<template>
  <AppLayout>
    <!-- Hero Banner -->
    <section
      class="relative py-16 md:py-24 bg-cover bg-center bg-no-repeat"
      style="background-image: url('/background.jpg')"
    >
      <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/55 to-black/35" aria-hidden="true" />
      <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">Des marques authentiques pour votre univers numérique</h1>
          <p class="text-lg md:text-xl text-yellow-100/90 mb-8">Produits 100% authentiques, origine européenne avec vraie garantie</p>
          <router-link to="/catalog" class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-600 to-amber-500 text-black px-8 py-3 rounded-lg font-semibold hover:from-yellow-500 hover:to-amber-400 transition shadow-lg">
            Découvrir nos produits
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Trust Badges -->
    <section class="bg-white border-b border-gray-100 py-10">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <div v-for="badge in trustBadges" :key="badge.title" class="flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-100 to-amber-100 rounded-2xl flex items-center justify-center mb-4">
              <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="badge.icon" /></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ badge.title }}</h3>
            <p class="text-sm text-gray-600">{{ badge.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section class="container mx-auto px-4 py-12">
      <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Catégories</h2>
        <router-link to="/catalog" class="text-yellow-600 hover:text-yellow-700 font-medium flex items-center gap-1 text-sm">
          Voir tout
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </router-link>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <router-link
          v-for="cat in productStore.categories.slice(0, 6)"
          :key="cat.id"
          :to="`/catalog?category=${cat.id}`"
          class="card-hover text-center p-4 group"
        >
          <div class="w-16 h-16 bg-gradient-to-br from-yellow-50 to-amber-50 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:from-yellow-100 group-hover:to-amber-100 transition">
            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
          </div>
          <span class="font-medium text-gray-800 group-hover:text-yellow-600 transition">{{ cat.name }}</span>
        </router-link>
      </div>
    </section>

    <!-- Popular Products -->
    <section class="bg-gray-50 py-12">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Produits populaires</h2>
          <router-link to="/catalog" class="text-yellow-600 hover:text-yellow-700 font-medium flex items-center gap-1 text-sm">
            Voir tout
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </router-link>
        </div>
        <div v-if="productStore.loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="n in 4" :key="n" class="card"><AppSkeleton :height="'14rem'" class="mb-4" /><AppSkeleton :count="2" /></div>
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <ProductCard v-for="product in productStore.products.slice(0, 8)" :key="product.id" :product="product" />
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-black py-12">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Besoin d'aide ?</h2>
        <p class="text-gray-300 mb-6">Notre équipe d'experts est à votre disposition pour vous conseiller</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
          <a href="tel:+261346623495" class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-600 to-amber-500 text-black px-6 py-3 rounded-lg font-semibold hover:from-yellow-500 hover:to-amber-400 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
            +261 34 66 234 95
          </a>
          <router-link to="/contact" class="inline-flex items-center gap-2 border-2 border-yellow-500 text-yellow-500 px-6 py-3 rounded-lg font-semibold hover:bg-yellow-500 hover:text-black transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            Nous contacter
          </router-link>
        </div>
      </div>
    </section>
  </AppLayout>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import ProductCard from '@/components/ProductCard.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import { useProductStore } from '@/stores/product'

const productStore = useProductStore()

const trustBadges = [
  { title: '100% Authentique', desc: 'Origine Européenne', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
  { title: 'Accompagnement', desc: 'Conseils d\'experts dédiés', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
  { title: 'Vraie Garantie', desc: 'Circuit Officiel Constructeur', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
  { title: 'Proche de vous', desc: 'Livraison à Tana et en province', icon: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z' },
]

onMounted(() => {
  productStore.fetchProducts()
  productStore.fetchCategories()
})
</script>
