import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export interface User {
  id: number
  name: string
  email: string
  role: string
  phone?: string
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token') || sessionStorage.getItem('token'))
  const initialized = ref(false)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function login(email: string, password: string, rememberMe: boolean = false) {
    loading.value = true
    try {
      const response = await api.post('/login', { email, password, remember_me: rememberMe })
      token.value = response.data.token
      user.value = response.data.user
      if (rememberMe) {
        localStorage.setItem('token', response.data.token)
      } else {
        sessionStorage.setItem('token', response.data.token)
      }
      return true
    } catch {
      return false
    } finally {
      loading.value = false
    }
  }

  async function register(name: string, email: string, password: string) {
    loading.value = true
    try {
      const response = await api.post('/register', { name, email, password })
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', response.data.token)
      return true
    } catch {
      return false
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch {
      // ignore
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      sessionStorage.removeItem('token')
    }
  }

  async function fetchUser() {
    if (!token.value) {
      initialized.value = true
      return
    }
    try {
      const response = await api.get('/user')
      user.value = response.data
    } catch {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
    } finally {
      initialized.value = true
    }
  }

  async function ensureInitialized() {
    if (!initialized.value) {
      await fetchUser()
    }
  }

  return {
    user, token, isAuthenticated, isAdmin, initialized, loading,
    login, register, logout, fetchUser, ensureInitialized,
  }
})
