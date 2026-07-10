import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export interface Product {
  id: number
  name: string
  description: string
  price: number
  stock: number
  image_url: string
  category_id: number
  category?: { id: number; name: string }
  size?: string
  color?: string
  brand?: string
  reviews?: any[]
  average_rating?: number
}

export const useProductStore = defineStore('product', () => {
  const products = ref<Product[]>([])
  const currentProduct = ref<Product | null>(null)
  const categories = ref<any[]>([])
  const loading = ref(false)

  async function fetchProducts(categoryId?: number, search?: string, filters?: Record<string, any>) {
    loading.value = true
    try {
      const params: Record<string, any> = {}
      if (categoryId) params.category_id = categoryId
      if (search) params.search = search
      if (filters?.minPrice) params.min_price = filters.minPrice
      if (filters?.maxPrice) params.max_price = filters.maxPrice

      const response = await api.get('/products', { params })
      let data: Product[] = response.data

      if (filters) {
        data = data.filter((product) => {
          if (filters.minPrice && product.price < filters.minPrice) return false
          if (filters.maxPrice && product.price > filters.maxPrice) return false
          if (filters.size && product.size !== filters.size) return false
          if (filters.color && product.color !== filters.color) return false
          if (filters.brand && product.brand !== filters.brand) return false
          return true
        })
      }

      products.value = data
    } catch (error) {
      console.error('Fetch products error:', error)
      products.value = []
    } finally {
      loading.value = false
    }
  }

  function getUniqueSizes() {
    return [...new Set(products.value.map((p) => p.size).filter(Boolean))]
  }

  function getUniqueColors() {
    return [...new Set(products.value.map((p) => p.color).filter(Boolean))]
  }

  function getUniqueBrands() {
    return [...new Set(products.value.map((p) => p.brand).filter(Boolean))]
  }

  async function fetchProduct(id: number) {
    loading.value = true
    try {
      const response = await api.get(`/products/${id}`)
      currentProduct.value = response.data
    } catch (error) {
      console.error('Fetch product error:', error)
      currentProduct.value = null
    } finally {
      loading.value = false
    }
  }

  async function fetchCategories() {
    try {
      const response = await api.get('/categories')
      categories.value = response.data
    } catch (error) {
      console.error('Fetch categories error:', error)
    }
  }

  return {
    products, currentProduct, categories, loading,
    fetchProducts, fetchProduct, fetchCategories,
    getUniqueSizes, getUniqueColors, getUniqueBrands,
  }
})
