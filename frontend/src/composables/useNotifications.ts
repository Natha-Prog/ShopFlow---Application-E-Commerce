import { ref, computed } from 'vue'

interface Notification {
  id: number
  type: 'order' | 'promotion' | 'account' | 'system'
  title: string
  message: string
  read: boolean
  createdAt: string
}

const notifications = ref<Notification[]>([])
const notificationPreferences = ref({
  email: {
    orders: true,
    promotions: true,
    account: true,
    newsletter: false
  },
  sms: {
    orders: false,
    promotions: false,
    account: false
  }
})

export function useNotifications() {
  const unreadCount = computed(() => notifications.value.filter(n => !n.read).length)
  
  function addNotification(notification: Omit<Notification, 'id' | 'read' | 'createdAt'>) {
    const newNotification: Notification = {
      ...notification,
      id: Date.now(),
      read: false,
      createdAt: new Date().toISOString()
    }
    notifications.value.unshift(newNotification)
    saveNotifications()
  }
  
  function markAsRead(id: number) {
    const notification = notifications.value.find(n => n.id === id)
    if (notification) {
      notification.read = true
      saveNotifications()
    }
  }
  
  function markAllAsRead() {
    notifications.value.forEach(n => n.read = true)
    saveNotifications()
  }
  
  function removeNotification(id: number) {
    notifications.value = notifications.value.filter(n => n.id !== id)
    saveNotifications()
  }
  
  function clearAll() {
    notifications.value = []
    saveNotifications()
  }
  
  function saveNotifications() {
    localStorage.setItem('notifications', JSON.stringify(notifications.value))
  }
  
  function loadNotifications() {
    const saved = localStorage.getItem('notifications')
    if (saved) {
      notifications.value = JSON.parse(saved)
    }
  }
  
  function updatePreferences(type: 'email' | 'sms', category: string, value: boolean) {
    if (type === 'email' && category in notificationPreferences.value.email) {
      (notificationPreferences.value.email as any)[category] = value
    } else if (type === 'sms' && category in notificationPreferences.value.sms) {
      (notificationPreferences.value.sms as any)[category] = value
    }
    savePreferences()
  }
  
  function savePreferences() {
    localStorage.setItem('notificationPreferences', JSON.stringify(notificationPreferences.value))
  }
  
  function loadPreferences() {
    const saved = localStorage.getItem('notificationPreferences')
    if (saved) {
      notificationPreferences.value = JSON.parse(saved)
    }
  }
  
  // Mock notification triggers
  function triggerOrderNotification(orderId: number, status: string) {
    addNotification({
      type: 'order',
      title: `Mise à jour de commande #${orderId}`,
      message: `Votre commande est maintenant ${status}`
    })
  }
  
  function triggerPromotionNotification(code: string, discount: string) {
    addNotification({
      type: 'promotion',
      title: 'Nouvelle offre disponible!',
      message: `Utilisez le code ${code} pour ${discount} de réduction`
    })
  }
  
  function triggerAccountNotification(message: string) {
    addNotification({
      type: 'account',
      title: 'Mise à jour du compte',
      message
    })
  }
  
  return {
    notifications,
    unreadCount,
    notificationPreferences,
    addNotification,
    markAsRead,
    markAllAsRead,
    removeNotification,
    clearAll,
    loadNotifications,
    updatePreferences,
    loadPreferences,
    triggerOrderNotification,
    triggerPromotionNotification,
    triggerAccountNotification
  }
}
