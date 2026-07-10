<template>
  <header class="bg-black shadow-lg sticky top-0 z-40">
    <!-- Top bar -->
    <div class="bg-gradient-to-r from-yellow-600 to-amber-500 py-1">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center text-xs text-black font-medium">
          <div class="flex items-center gap-4">
            <span class="flex items-center gap-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              +261 34 66 234 95
            </span>
            <span class="hidden sm:flex items-center gap-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              goldenshopmdg@gmail.com
            </span>
          </div>
          <div class="flex items-center gap-2">
            <span class="font-bold text-white">PAR LE MEILLEUR ET POUR LE MEILLEUR</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="hidden sm:inline">Livraison à Tana et en province</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Main header -->
    <div class="border-b border-yellow-600/20">
      <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
          <router-link to="/" class="flex items-center shrink-0 gap-2">
            <img src="/logo.png" alt="Golden Shop Mdg" class="h-10 w-auto" onerror="this.style.display='none'" />
            <span class="text-xl font-bold text-white hidden sm:block">Golden-Shop MDG</span>
          </router-link>

          <nav class="hidden lg:flex items-center gap-8">
            <router-link 
              v-for="link in navLinks" 
              :key="link.to" 
              :to="link.to" 
              class="relative text-yellow-100/90 hover:text-yellow-400 transition text-sm font-medium py-2"
              :class="{ 'text-yellow-400': isActiveLink(link.to) }"
            >
              {{ link.label }}
              <span 
                v-if="isActiveLink(link.to)"
                class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-yellow-400 to-amber-400 rounded-full"
              ></span>
            </router-link>
          </nav>

          <div class="flex items-center gap-3">
            <LocaleSelector />

            <router-link v-if="authStore.isAuthenticated" to="/favorites" class="relative p-2 group">
              <svg class="w-6 h-6 text-yellow-400 group-hover:text-yellow-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              </svg>
              <span v-if="favoritesStore.favorites.length > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">{{ favoritesStore.favorites.length }}</span>
            </router-link>

            <router-link v-if="authStore.isAuthenticated" to="/cart" class="relative p-2 group">
              <svg class="w-6 h-6 text-yellow-400 group-hover:text-yellow-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span v-if="cartStore.itemCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">{{ cartStore.itemCount }}</span>
            </router-link>

            <div v-if="authStore.isAuthenticated" class="hidden md:block relative" ref="userMenuRef">
              <button 
                @click="userMenuOpen = !userMenuOpen"
                class="text-yellow-100/90 text-sm font-medium flex items-center gap-1 hover:text-yellow-400 transition"
                :class="{ 'text-yellow-400': userMenuOpen }"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <svg 
                  class="w-4 h-4 transition-transform duration-200" 
                  :class="{ 'rotate-180': userMenuOpen }"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
              >
                <div 
                  v-if="userMenuOpen"
                  class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 border border-gray-100 z-50"
                >
                  <router-link to="/profile" @click="userMenuOpen = false" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 text-sm transition-colors">Profil</router-link>
                  <router-link to="/orders" @click="userMenuOpen = false" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 text-sm transition-colors">Mes commandes</router-link>
                  <router-link v-if="authStore.isAdmin" to="/admin/dashboard" @click="userMenuOpen = false" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 text-sm transition-colors">Back-office</router-link>
                  <hr class="my-2 border-gray-100">
                  <button @click="handleLogout" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-red-50 text-sm transition-colors">Déconnexion</button>
                </div>
              </Transition>
            </div>

            <div v-else class="hidden md:flex gap-2">
              <router-link to="/login" class="text-yellow-100/90 hover:text-yellow-400 text-sm font-medium px-3 py-2 transition">Connexion</router-link>
              <router-link to="/register" class="bg-gradient-to-r from-yellow-600 to-amber-500 text-black px-4 py-2 rounded-lg text-sm font-semibold hover:from-yellow-500 hover:to-amber-400 transition shadow-md">Inscription</router-link>
            </div>

            <button class="lg:hidden p-2 text-yellow-400" @click="mobileOpen = true" aria-label="Menu">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile drawer -->
    <Teleport to="body">
      <div v-if="mobileOpen" class="fixed inset-0 z-50 lg:hidden">
        <div class="fixed inset-0 bg-black/50" @click="mobileOpen = false" />
        <div class="fixed inset-y-0 right-0 w-80 bg-white shadow-2xl flex flex-col">
          <div class="bg-gradient-to-r from-yellow-600 to-amber-500 p-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
              <img src="/logo.png" alt="Golden Shop Mdg" class="h-8 w-auto" onerror="this.style.display='none'" />
              <span class="text-lg font-bold text-black">Golden-Shop MDG</span>
            </div>
            <button @click="mobileOpen = false" class="text-black text-2xl font-bold">&times;</button>
          </div>
          <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <router-link 
              v-for="link in navLinks" 
              :key="link.to" 
              :to="link.to" 
              class="block px-4 py-3 rounded-lg font-medium transition relative"
              :class="isActiveLink(link.to) ? 'text-yellow-700 bg-yellow-50' : 'text-gray-700 hover:bg-yellow-50'"
              @click="mobileOpen = false"
            >
              {{ link.label }}
              <span 
                v-if="isActiveLink(link.to)"
                class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-yellow-600 to-amber-500 rounded-r-full"
              ></span>
            </router-link>
          </nav>
          <div class="p-4 border-t border-gray-200 space-y-3 bg-gray-50">
            <div>
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-1">Langue & devise</p>
              <LocaleSelector variant="mobile" />
            </div>
            <template v-if="authStore.isAuthenticated">
              <div class="flex items-center gap-2 px-4">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-sm text-gray-600 font-medium">{{ authStore.user?.name }}</span>
              </div>
              <router-link to="/profile" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-yellow-50 font-medium transition" @click="mobileOpen = false">Profil</router-link>
              <router-link to="/orders" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-yellow-50 font-medium transition" @click="mobileOpen = false">Mes commandes</router-link>
              <router-link v-if="authStore.isAdmin" to="/admin/dashboard" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-yellow-50 font-medium transition" @click="mobileOpen = false">Back-office</router-link>
              <button @click="handleLogout" class="w-full btn-secondary text-sm">Déconnexion</button>
            </template>
            <template v-else>
              <router-link to="/login" class="block btn-secondary text-center text-sm" @click="mobileOpen = false">Connexion</router-link>
              <router-link to="/register" class="block btn-primary text-center text-sm" @click="mobileOpen = false">Inscription</router-link>
            </template>
          </div>
        </div>
      </div>
    </Teleport>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useFavoritesStore } from '@/stores/favorites'
import LocaleSelector from '@/components/LocaleSelector.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()

const mobileOpen = ref(false)
const userMenuOpen = ref(false)
const userMenuRef = ref<HTMLElement | null>(null)

const navLinks = computed(() => {
  const links = [
    { to: '/', label: 'Accueil' },
    { to: '/catalog', label: 'Catalogue' },
    { to: '/new', label: 'Nouveautés' },
    { to: '/about', label: 'À propos' },
    { to: '/faq', label: 'FAQ' },
    { to: '/contact', label: 'Contact' },
  ]
  return links
})

function isActiveLink(path: string): boolean {
  if (path === '/') {
    return route.path === '/'
  }
  return route.path.startsWith(path)
}

async function handleLogout() {
  await authStore.logout()
  mobileOpen.value = false
  userMenuOpen.value = false
  router.push('/')
}

function handleUserMenuClickOutside(e: MouseEvent) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target as Node)) {
    userMenuOpen.value = false
  }
}

onMounted(() => {
  if (authStore.isAuthenticated) {
    cartStore.fetchCart()
    favoritesStore.fetchFavorites()
  }
  document.addEventListener('click', handleUserMenuClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleUserMenuClickOutside)
})
</script>
