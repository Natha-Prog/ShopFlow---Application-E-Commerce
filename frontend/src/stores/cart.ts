import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

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

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([])
  const loading = ref(false)

  const total = computed(() =>
    items.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0)
  )

  const itemCount = computed(() =>
    items.value.reduce((sum, item) => sum + item.quantity, 0)
  )

  async function fetchCart() {
    loading.value = true
    try {
      const response = await api.get('/cart')
      items.value = response.data
    } catch {
      items.value = []
    } finally {
      loading.value = false
    }
  }

  async function addToCart(productId: number, quantity = 1) {
    await api.post('/cart', { product_id: productId, quantity })
    await fetchCart()
  }

  async function updateCartItem(itemId: number, quantity: number) {
    await api.put(`/cart/${itemId}`, { quantity })
    await fetchCart()
  }

  async function removeFromCart(itemId: number) {
    await api.delete(`/cart/${itemId}`)
    await fetchCart()
  }

  async function clearCart() {
    items.value = []
  }

  return { items, loading, total, itemCount, fetchCart, addToCart, updateCartItem, removeFromCart, clearCart }
})
