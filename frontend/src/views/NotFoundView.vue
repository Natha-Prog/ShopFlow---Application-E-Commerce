<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-12 text-center">
      <h1 class="text-6xl font-bold text-gray-300 mb-4">404</h1>
      <p class="text-xl text-gray-600 mb-8">Page introuvable</p>
      <router-link to="/" class="btn-primary">Retour à l'accueil</router-link>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
</script>
