<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">À propos de Golden-Shop MDG</h1>
      
      <div class="max-w-4xl mx-auto space-y-8">
        <div class="card">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Notre histoire</h2>
          <p class="text-gray-600 leading-relaxed">
            Golden-Shop MDG est votre boutique en ligne de confiance à Madagascar. Depuis notre création, nous nous engageons à offrir des produits de qualité exceptionnelle à nos clients, avec un service client irréprochable et une livraison rapide dans tout le pays.
          </p>
        </div>

        <div class="card">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Notre mission</h2>
          <p class="text-gray-600 leading-relaxed">
            Notre mission est de rendre l'achat en ligne accessible, simple et sécurisé pour tous les Malgaches. Nous sélectionnons soigneusement chaque produit pour garantir la satisfaction de nos clients.
          </p>
        </div>

        <div class="card">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Nos valeurs</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="w-16 h-16 bg-gold-pale rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="font-semibold text-gray-800 mb-2">Qualité</h3>
              <p class="text-gray-600 text-sm">Produits soigneusement sélectionnés</p>
            </div>
            <div class="text-center">
              <div class="w-16 h-16 bg-gold-pale rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="font-semibold text-gray-800 mb-2">Rapidité</h3>
              <p class="text-gray-600 text-sm">Livraison rapide à Antananarivo et en province</p>
            </div>
            <div class="text-center">
              <div class="w-16 h-16 bg-gold-pale rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </div>
              <h3 class="font-semibold text-gray-800 mb-2">Satisfaction</h3>
              <p class="text-gray-600 text-sm">Service client dédié</p>
            </div>
          </div>
        </div>

        <div class="card">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Contactez-nous</h2>
          <div class="space-y-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gold-pale rounded-full flex items-center justify-center text-gold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <p class="font-semibold">Adresse</p>
                <p class="text-gray-600">Lot III F 179 - Antohomadinika - Antananarivo - Madagascar</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gold-pale rounded-full flex items-center justify-center text-gold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div>
                <p class="font-semibold">Téléphone</p>
                <p class="text-gray-600">+261 34 66 234 95</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gold-pale rounded-full flex items-center justify-center text-gold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="font-semibold">Email</p>
                <p class="text-gray-600">goldenshopmdg@gmail.com</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
</script>
