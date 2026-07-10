<script setup lang="ts">
defineProps<{
  show: boolean
  title?: string
  size?: 'sm' | 'md' | 'lg'
}>()

defineEmits<{ close: [] }>()

const sizeClass = { sm: 'max-w-md', md: 'max-w-lg', lg: 'max-w-2xl' }
</script>

<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/50" @click="$emit('close')" />
      <div class="relative bg-white rounded-xl shadow-xl w-full" :class="sizeClass[size || 'md']">
        <div v-if="title" class="flex items-center justify-between px-6 py-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6">
          <slot />
        </div>
      </div>
    </div>
  </Teleport>
</template>
