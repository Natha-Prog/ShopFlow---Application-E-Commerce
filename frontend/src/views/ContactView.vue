<template>
  <AppLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Contactez-nous</h1>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="card">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Envoyez-nous un message</h2>
          <form @submit.prevent="submitContact" class="space-y-4">
            <AppInput v-model="contactForm.name" label="Nom" type="text" required />
            <AppInput v-model="contactForm.email" label="Email" type="email" required />
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Sujet</label>
              <select v-model="contactForm.subject" required class="input-field">
                <option value="">Sélectionnez un sujet</option>
                <option value="order">Question sur une commande</option>
                <option value="product">Question sur un produit</option>
                <option value="return">Retour ou échange</option>
                <option value="payment">Problème de paiement</option>
                <option value="other">Autre</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
              <textarea
                v-model="contactForm.message"
                rows="5"
                required
                class="input-field"
                placeholder="Décrivez votre demande..."
              ></textarea>
            </div>
            <AppButton type="submit" :loading="sending" class="w-full">Envoyer le message</AppButton>
          </form>
        </div>

        <div class="space-y-6">
          <div class="card">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Informations de contact</h2>
            <div class="space-y-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gold-pale rounded-full flex items-center justify-center text-gold">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold">Email</p>
                  <p class="text-gray-600">goldenshopmdg@gmail.com</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gold-pale rounded-full flex items-center justify-center text-gold">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold">Téléphone</p>
                  <p class="text-gray-600">+261 34 66 234 95</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gold-pale rounded-full flex items-center justify-center text-gold">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold">Adresse</p>
                  <p class="text-gray-600">Lot III F 179 - Antohomadinika - Antananarivo - Madagascar</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppButton from '@/components/ui/AppButton.vue'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'

const toast = useToast()

const contactForm = ref({
  name: '',
  email: '',
  subject: '',
  message: '',
})

const sending = ref(false)

async function submitContact() {
  sending.value = true
  try {
    await api.post('/contact', contactForm.value)
    toast.success('Votre message a été envoyé avec succès')
    contactForm.value = { name: '', email: '', subject: '', message: '' }
  } catch {
    toast.error('Erreur lors de l\'envoi du message')
  } finally {
    sending.value = false
  }
}
</script>
