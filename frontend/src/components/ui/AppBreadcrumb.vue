<script setup lang="ts">
defineProps<{ items: { label: string; to?: string }[] }>()
</script>

<template>
  <nav class="flex items-center gap-2 text-sm text-gray-500 mb-4">
    <template v-for="(item, i) in items" :key="i">
      <span v-if="i > 0" class="text-gray-300">/</span>
      <router-link v-if="item.to" :to="item.to" class="hover:text-gold transition">{{ item.label }}</router-link>
      <span v-else class="text-gray-900 font-medium">{{ item.label }}</span>
    </template>
  </nav>
</template>
