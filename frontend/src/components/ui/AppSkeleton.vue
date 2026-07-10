<script setup lang="ts">
defineProps<{ count?: number; height?: string }>()
</script>

<template>
  <div class="skeleton" :style="{ height: height || '1rem', width: '100%' }" v-for="n in (count || 1)" :key="n" />
</template>
