<script setup lang="ts">
defineProps<{
  variant?: 'primary' | 'secondary' | 'danger'
  type?: 'button' | 'submit'
  disabled?: boolean
  loading?: boolean
}>()
</script>

<template>
  <button
    :type="type || 'button'"
    :disabled="disabled || loading"
    class="inline-flex items-center justify-center gap-2 min-h-[44px]"
    :class="{
      'btn-primary': variant === 'primary' || !variant,
      'btn-secondary': variant === 'secondary',
      'btn-danger': variant === 'danger',
    }"
  >
    <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>
    <slot />
  </button>
</template>
