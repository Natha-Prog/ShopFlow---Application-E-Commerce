<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-12 flex justify-center">
      <div class="card w-full max-w-md text-center">
        <div class="mb-6">
          <svg class="w-16 h-16 mx-auto text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
        </div>
        
        <div v-if="verified">
          <h1 class="text-2xl font-bold text-gray-800 mb-4">Email vérifié !</h1>
          <p class="text-gray-600 mb-6">
            Votre adresse email a été vérifiée avec succès. Vous pouvez maintenant utiliser votre compte.
          </p>
          <AppButton @click="router.push('/')" class="w-full">
            Aller à l'accueil
          </AppButton>
        </div>
        
        <div v-else>
          <h1 class="text-2xl font-bold text-gray-800 mb-4">Vérifiez votre email</h1>
          <p class="text-gray-600 mb-6">
            Nous avons envoyé un code de vérification à 6 chiffres à votre adresse email. Entrez ce code ci-dessous pour activer votre compte.
          </p>
          <p v-if="authStore.user" class="text-sm text-gray-500 mb-6">
            Email: {{ authStore.user.email }}
          </p>
          
          <form @submit.prevent="handleVerify" class="space-y-4">
            <div class="relative">
              <input 
                v-model="code"
                type="text" 
                maxlength="6"
                placeholder="000000"
                class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-600 text-center text-2xl tracking-widest"
                required
              />
            </div>
            
            <AppButton type="submit" :loading="loading" class="w-full">
              Vérifier
            </AppButton>
          </form>
          
          <div class="mt-4">
            <button @click="handleResend" :disabled="loading" class="text-sm text-gray-600 hover:text-yellow-600 transition">
              Renvoyer le code
            </button>
          </div>
          
          <p v-if="error" class="mt-4 text-red-600 text-sm">
            {{ error }}
          </p>
        </div>
        
        <p class="text-center mt-6 text-gray-600 text-sm">
          <router-link to="/" class="text-gold hover:underline font-medium">Retour à l'accueil</router-link>
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import AppButton from '@/components/ui/AppButton.vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const loading = ref(false)
const verified = ref(false)
const error = ref('')
const code = ref('')

async function handleVerify() {
  loading.value = true
  error.value = ''
  try {
    await api.post('/email/verify', { code: code.value })
    verified.value = true
    toast.success('Email vérifié avec succès')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Code invalide ou expiré'
  } finally {
    loading.value = false
  }
}

async function handleResend() {
  loading.value = true
  try {
    await api.post('/email/resend')
    toast.success('Code de vérification renvoyé')
  } catch {
    toast.error('Erreur lors de l\'envoi du code')
  } finally {
    loading.value = false
  }
}
</script>
