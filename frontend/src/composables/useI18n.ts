import { ref, computed } from 'vue'

const translations = {
  fr: {
    nav: {
      home: 'Accueil',
      catalog: 'Catalogue',
      new: 'Nouveautés',
      about: 'À propos',
      faq: 'FAQ',
      contact: 'Contact',
      orders: 'Mes commandes',
      profile: 'Mon Profil',
      admin: 'Admin',
      login: 'Connexion',
      register: 'Inscription',
      logout: 'Déconnexion'
    },
    home: {
      welcome: 'Bienvenue sur ShopFlow',
      popularProducts: 'Produits populaires'
    },
    catalog: {
      title: 'Catalogue',
      search: 'Rechercher...',
      filters: 'Filtres',
      priceRange: 'Gamme de prix',
      size: 'Taille',
      color: 'Couleur',
      brand: 'Marque',
      resetFilters: 'Réinitialiser les filtres',
      noResults: 'Aucun résultat'
    },
    product: {
      addToCart: 'Ajouter au panier',
      loginToAdd: 'Connectez-vous pour ajouter au panier',
      stock: 'Stock',
      brand: 'Marque',
      size: 'Taille',
      color: 'Couleur',
      quantity: 'Quantité',
      reviews: 'Avis clients',
      giveReview: 'Donner votre avis',
      rating: 'Note',
      comment: 'Commentaire',
      publishReview: 'Publier l\'avis',
      loginToReview: 'Connectez-vous pour laisser un avis'
    },
    cart: {
      title: 'Mon Panier',
      empty: 'Votre panier est vide',
      total: 'Total',
      checkout: 'Commander',
      remove: 'Supprimer'
    },
    checkout: {
      title: 'Commander',
      address: 'Adresse de livraison',
      shippingMethod: 'Mode de livraison',
      paymentMethod: 'Mode de paiement',
      promoCode: 'Code promo',
      apply: 'Appliquer',
      orderSummary: 'Résumé de la commande',
      subtotal: 'Sous-total',
      discount: 'Remise',
      shipping: 'Livraison',
      paymentFee: 'Frais de paiement',
      total: 'Total',
      confirmOrder: 'Confirmer la commande'
    },
    orders: {
      title: 'Mes commandes',
      noOrders: 'Vous n\'avez pas encore de commandes',
      discoverCatalog: 'Découvrir le catalogue',
      orderTracking: 'Suivi de commande',
      items: 'Articles',
      shippingAddress: 'Adresse de livraison'
    },
    profile: {
      title: 'Mon Profil',
      personalInfo: 'Informations personnelles',
      addresses: 'Adresses de livraison',
      addAddress: 'Ajouter une adresse',
      favorites: 'Favoris',
      noFavorites: 'Aucun favori pour le moment',
      changePassword: 'Changer le mot de passe',
      update: 'Mettre à jour'
    },
    contact: {
      title: 'Contactez-nous',
      sendMessage: 'Envoyez-nous un message',
      name: 'Nom',
      email: 'Email',
      subject: 'Sujet',
      message: 'Message',
      send: 'Envoyer le message',
      contactInfo: 'Informations de contact',
      liveChat: 'Chat en direct',
      startChat: 'Démarrer le chat',
      followUs: 'Suivez-nous'
    },
    faq: {
      title: 'Questions Fréquemment Posées',
      search: 'Rechercher une question...',
      all: 'Toutes',
      noResults: 'Aucune question trouvée pour votre recherche.',
      notFound: 'Vous ne trouvez pas votre réponse?',
      contactUs: 'Contactez-nous'
    }
  },
  en: {
    nav: {
      home: 'Home',
      catalog: 'Catalog',
      new: 'New Arrivals',
      about: 'About',
      faq: 'FAQ',
      contact: 'Contact',
      orders: 'My Orders',
      profile: 'My Profile',
      admin: 'Admin',
      login: 'Login',
      register: 'Register',
      logout: 'Logout'
    },
    home: {
      welcome: 'Welcome to ShopFlow',
      popularProducts: 'Popular Products'
    },
    catalog: {
      title: 'Catalog',
      search: 'Search...',
      filters: 'Filters',
      priceRange: 'Price Range',
      size: 'Size',
      color: 'Color',
      brand: 'Brand',
      resetFilters: 'Reset Filters',
      noResults: 'No results'
    },
    product: {
      addToCart: 'Add to Cart',
      loginToAdd: 'Login to add to cart',
      stock: 'Stock',
      brand: 'Brand',
      size: 'Size',
      color: 'Color',
      quantity: 'Quantity',
      reviews: 'Customer Reviews',
      giveReview: 'Leave a Review',
      rating: 'Rating',
      comment: 'Comment',
      publishReview: 'Publish Review',
      loginToReview: 'Login to leave a review'
    },
    cart: {
      title: 'My Cart',
      empty: 'Your cart is empty',
      total: 'Total',
      checkout: 'Checkout',
      remove: 'Remove'
    },
    checkout: {
      title: 'Checkout',
      address: 'Shipping Address',
      shippingMethod: 'Shipping Method',
      paymentMethod: 'Payment Method',
      promoCode: 'Promo Code',
      apply: 'Apply',
      orderSummary: 'Order Summary',
      subtotal: 'Subtotal',
      discount: 'Discount',
      shipping: 'Shipping',
      paymentFee: 'Payment Fee',
      total: 'Total',
      confirmOrder: 'Confirm Order'
    },
    orders: {
      title: 'My Orders',
      noOrders: 'You have no orders yet',
      discoverCatalog: 'Discover the catalog',
      orderTracking: 'Order Tracking',
      items: 'Items',
      shippingAddress: 'Shipping Address'
    },
    profile: {
      title: 'My Profile',
      personalInfo: 'Personal Information',
      addresses: 'Shipping Addresses',
      addAddress: 'Add Address',
      favorites: 'Favorites',
      noFavorites: 'No favorites yet',
      changePassword: 'Change Password',
      update: 'Update'
    },
    contact: {
      title: 'Contact Us',
      sendMessage: 'Send us a message',
      name: 'Name',
      email: 'Email',
      subject: 'Subject',
      message: 'Message',
      send: 'Send Message',
      contactInfo: 'Contact Information',
      liveChat: 'Live Chat',
      startChat: 'Start Chat',
      followUs: 'Follow Us'
    },
    faq: {
      title: 'Frequently Asked Questions',
      search: 'Search a question...',
      all: 'All',
      noResults: 'No questions found for your search.',
      notFound: 'Can\'t find your answer?',
      contactUs: 'Contact Us'
    }
  }
}

const currentLanguage = ref(localStorage.getItem('language') || 'fr')

export function useI18n() {
  const t = computed(() => {
    const lang = currentLanguage.value as keyof typeof translations
    return translations[lang] || translations.fr
  })
  
  function setLanguage(lang: 'fr' | 'en') {
    currentLanguage.value = lang
    localStorage.setItem('language', lang)
  }
  
  function getLanguage() {
    return currentLanguage.value
  }
  
  return {
    t,
    setLanguage,
    getLanguage
  }
}
