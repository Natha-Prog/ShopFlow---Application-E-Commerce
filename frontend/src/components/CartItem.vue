<template>
  <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
    <img 
      :src="item.product.image_url || 'https://via.placeholder.com/100'" 
      :alt="item.product.name"
      class="w-24 h-24 object-cover rounded"
    />
    <div class="flex-1">
      <h3 class="font-semibold text-gray-800">{{ item.product.name }}</h3>
      <p class="text-gray-600">{{ item.product.price }} MGA</p>
    </div>
    <div class="flex items-center gap-2">
      <button 
        @click="updateQuantity(item.quantity - 1)"
        :disabled="item.quantity <= 1"
        class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300 transition disabled:opacity-50"
      >
        -
      </button>
      <span class="w-8 text-center">{{ item.quantity }}</span>
      <button 
        @click="updateQuantity(item.quantity + 1)"
        class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300 transition"
      >
        +
      </button>
    </div>
    <div class="text-right">
      <p class="font-bold text-gray-800">{{ (item.product.price * item.quantity).toFixed(2) }} MGA</p>
      <button 
        @click="removeItem"
        class="text-red-600 hover:text-red-800 text-sm mt-2"
      >
        Supprimer
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCartStore } from '@/stores/cart'

interface CartItem {
  id: number
  product_id: number
  quantity: number
  product: {
    id: number
    name: string
    price: number
    image_url: string
  }
}

const props = defineProps<{
  item: CartItem
}>()

const cartStore = useCartStore()

function updateQuantity(quantity: number) {
  if (quantity >= 1) {
    cartStore.updateCartItem(props.item.id, quantity)
  }
}

function removeItem() {
  cartStore.removeFromCart(props.item.id)
}
</script>
