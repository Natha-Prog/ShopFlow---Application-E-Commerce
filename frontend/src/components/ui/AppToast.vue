<script setup lang="ts">
import { useToast } from '@/composables/useToast'

const { toasts, remove } = useToast()

const typeClasses: Record<string, string> = {
  success: 'bg-green-600',
  error: 'bg-red-600',
  info: 'bg-yellow-700',
  warning: 'bg-yellow-600',
}
</script>

<template>
  <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 max-w-sm w-full pointer-events-none">
    <div
      v-for="toast in toasts"
      :key="toast.id"
      class="toast-enter pointer-events-auto flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-white shadow-lg"
      :class="typeClasses[toast.type]"
    >
      <span class="text-sm font-medium">{{ toast.message }}</span>
      <button @click="remove(toast.id)" class="opacity-80 hover:opacity-100 text-lg leading-none">&times;</button>
    </div>
  </div>
</template>
