<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-12 flex justify-center">
      <div class="card w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Connexion</h1>
        <form @submit.prevent="handleLogin" class="space-y-4">
          <AppInput v-model="email" label="Email" type="email" required />
          <div class="relative">
            <AppInput 
              v-model="password" 
              label="Mot de passe" 
              :type="showPassword ? 'text' : 'password'" 
              required 
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-9 text-gray-500 hover:text-gray-700"
            >
              <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
            </button>
          </div>
          <div class="flex items-center gap-2">
            <input 
              type="checkbox" 
              id="remember" 
              v-model="rememberMe"
              class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500"
            />
            <label for="remember" class="text-sm text-gray-600">Se souvenir de moi</label>
          </div>
          <AppButton type="submit" :loading="loading" class="w-full">Se connecter</AppButton>
        </form>
        <p class="text-center mt-4 text-gray-600 text-sm">
          Pas encore de compte ?
          <router-link to="/register" class="text-gold hover:underline font-medium">S'inscrire</router-link>
        </p>
        <p class="text-center mt-2 text-xs text-gray-400">Admin: admin@golden-shop.mg / Admin@2024</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppButton from '@/components/ui/AppButton.vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import { useCartStore } from '@/stores/cart'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()
const toast = useToast()

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const rememberMe = ref(false)
const loading = ref(false)

async function handleLogin() {
  loading.value = true
  const success = await authStore.login(email.value, password.value, rememberMe.value)
  loading.value = false
  if (success) {
    await cartStore.fetchCart()
    toast.success('Connexion réussie')
    router.push((route.query.redirect as string) || '/')
  } else {
    toast.error('Email ou mot de passe incorrect')
  }
}
</script>
