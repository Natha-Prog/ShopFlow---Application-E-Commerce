<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const authStore = useAuthStore()
const sidebarOpen = ref(false)

const navItems = [
  { to: '/admin/dashboard', label: 'Tableau de bord', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
  { to: '/admin/products', label: 'Produits', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
  { to: '/admin/categories', label: 'Catégories', icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z' },
  { to: '/admin/orders', label: 'Commandes', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
  { to: '/admin/promotions', label: 'Promotions', icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z' },
  { to: '/admin/users', label: 'Utilisateurs', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197' },
]

const pageTitles: Record<string, string> = {
  '/admin/dashboard': 'Tableau de bord',
  '/admin/products': 'Produits',
  '/admin/categories': 'Catégories',
  '/admin/orders': 'Commandes',
  '/admin/promotions': 'Promotions',
  '/admin/users': 'Utilisateurs',
}

const pageTitle = computed(() => {
  const match = Object.entries(pageTitles).find(([path]) => route.path.startsWith(path))
  return match?.[1] ?? 'Administration'
})

const userInitials = computed(() => {
  const name = authStore.user?.name ?? 'A'
  return name.split(' ').map((n: string) => n[0]).join('').slice(0, 2).toUpperCase()
})

function isActive(path: string) {
  return route.path === path || route.path.startsWith(path + '/')
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex">
    <div v-if="sidebarOpen" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden" @click="sidebarOpen = false" />

    <!-- Sidebar -->
    <aside
      class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-black text-white flex flex-col transform transition-transform duration-300 lg:translate-x-0 border-r border-yellow-600/10"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="px-5 py-5 border-b border-yellow-600/10">
        <router-link to="/admin/dashboard" class="flex items-center gap-3" @click="sidebarOpen = false">
          <img src="/logo.png" alt="Golden Shop Mdg" class="h-9 w-auto" />
        </router-link>
        <p class="mt-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-yellow-600/70">Espace administration</p>
      </div>

      <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <p class="px-4 mb-2 text-[10px] font-semibold uppercase tracking-wider text-gray-500">Menu principal</p>
        <router-link
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="admin-nav-item"
          :class="{ 'admin-nav-active': isActive(item.to) }"
          @click="sidebarOpen = false"
        >
          <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="item.icon" />
          </svg>
          {{ item.label }}
        </router-link>
      </nav>

      <div class="p-4 border-t border-yellow-600/10 space-y-3">
        <router-link
          to="/"
          class="flex items-center gap-2 text-sm text-gray-400 hover:text-gold-light transition px-3 py-2 rounded-lg hover:bg-white/5"
          @click="sidebarOpen = false"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Retour à la boutique
        </router-link>
        <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-white/5">
          <div class="w-9 h-9 rounded-full bg-gradient-to-br from-yellow-600 to-amber-500 flex items-center justify-center text-black text-xs font-bold shrink-0">
            {{ userInitials }}
          </div>
          <div class="min-w-0">
            <p class="text-sm font-medium text-white truncate">{{ authStore.user?.name }}</p>
            <p class="text-xs text-yellow-600/80">Administrateur</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">
      <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-gray-200 px-4 lg:px-8 py-4 flex items-center gap-4">
        <button class="lg:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg transition" @click="sidebarOpen = true" aria-label="Ouvrir le menu">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <div class="flex-1 min-w-0">
          <h1 class="text-lg font-bold text-gray-900 truncate">{{ pageTitle }}</h1>
          <p class="text-xs text-gray-500 hidden sm:block">Golden Shop Mdg — Panneau de gestion</p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
          <router-link to="/" class="hidden sm:inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gold-dark font-medium px-3 py-2 rounded-lg hover:bg-gold-pale transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Boutique
          </router-link>
          <span class="hidden md:inline badge bg-gold-pale text-gold-dark border border-yellow-200">Admin</span>
        </div>
      </header>

      <main class="flex-1 p-4 lg:p-8 overflow-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
