<script setup lang="ts">
defineProps<{ status: string }>()

const statusMap: Record<string, string> = {
  pending: 'badge-pending',
  processing: 'badge-processing',
  shipped: 'badge-shipped',
  delivered: 'badge-delivered',
  cancelled: 'badge-cancelled',
}

const labelMap: Record<string, string> = {
  pending: 'En attente',
  processing: 'En traitement',
  shipped: 'Expédiée',
  delivered: 'Livrée',
  cancelled: 'Annulée',
}
</script>

<template>
  <span class="badge" :class="statusMap[status] || 'bg-gray-100 text-gray-800'">
    {{ labelMap[status] || status }}
  </span>
</template>
