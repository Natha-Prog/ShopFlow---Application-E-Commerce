import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: () => import('@/views/HomeView.vue') },
    { path: '/catalog', name: 'catalog', component: () => import('@/views/CatalogView.vue') },
    { path: '/new', name: 'new', component: () => import('@/views/NewView.vue') },
    { path: '/about', name: 'about', component: () => import('@/views/AboutView.vue') },
    { path: '/product/:id', name: 'product-detail', component: () => import('@/views/ProductDetailView.vue') },
    { path: '/login', name: 'login', component: () => import('@/views/LoginView.vue') },
    { path: '/register', name: 'register', component: () => import('@/views/RegisterView.vue') },
    { path: '/verify-email', name: 'verify-email', component: () => import('@/views/VerifyEmailView.vue'), meta: { requiresAuth: true } },
    { path: '/cart', name: 'cart', component: () => import('@/views/CartView.vue'), meta: { requiresAuth: true } },
    { path: '/favorites', name: 'favorites', component: () => import('@/views/FavoritesView.vue'), meta: { requiresAuth: true } },
    { path: '/checkout', name: 'checkout', component: () => import('@/views/CheckoutView.vue'), meta: { requiresAuth: true } },
    { path: '/orders', name: 'orders', component: () => import('@/views/OrdersView.vue'), meta: { requiresAuth: true } },
    { path: '/orders/:id/confirmation', name: 'order-confirmation', component: () => import('@/views/OrderConfirmationView.vue'), meta: { requiresAuth: true } },
    { path: '/profile', name: 'profile', component: () => import('@/views/ProfileView.vue'), meta: { requiresAuth: true } },
    { path: '/contact', name: 'contact', component: () => import('@/views/ContactView.vue') },
    { path: '/faq', name: 'faq', component: () => import('@/views/FAQView.vue') },
    { path: '/forbidden', name: 'forbidden', component: () => import('@/views/ForbiddenView.vue') },
    { path: '/admin', redirect: '/admin/dashboard' },
    { path: '/admin/dashboard', name: 'admin-dashboard', component: () => import('@/views/admin/DashboardView.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/admin/products', name: 'admin-products', component: () => import('@/views/admin/ProductsView.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/admin/categories', name: 'admin-categories', component: () => import('@/views/admin/CategoriesView.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/admin/orders', name: 'admin-orders', component: () => import('@/views/admin/OrdersView.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/admin/promotions', name: 'admin-promotions', component: () => import('@/views/admin/PromotionsView.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/admin/users', name: 'admin-users', component: () => import('@/views/admin/UsersView.vue'), meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('@/views/NotFoundView.vue') },
  ],
})

router.beforeEach(async (to) => {
  const authStore = useAuthStore()
  await authStore.ensureInitialized()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { path: '/login', query: { redirect: to.fullPath } }
  }

  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    return { path: '/forbidden' }
  }
})

export default router
