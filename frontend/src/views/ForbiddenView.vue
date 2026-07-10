<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-12 text-center">
      <h1 class="text-4xl font-bold text-gray-800 mb-4">Accès refusé</h1>
      <p class="text-gray-600 mb-8">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
      <router-link to="/" class="btn-primary">Retour à l'accueil</router-link>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
</script>
