<template>
  <footer class="bg-black text-gray-400 mt-auto border-t border-yellow-600/20">
    <div class="container mx-auto px-4 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <img src="/logo.png" alt="Golden Shop Mdg" class="h-14 w-auto mb-4" />
          <p class="text-sm leading-relaxed">Votre boutique en ligne de confiance. Produits de qualité, livraison rapide et service client exceptionnel.</p>
        </div>
        <div>
          <h4 class="text-gold-light font-semibold mb-4">Navigation</h4>
          <ul class="space-y-2 text-sm">
            <li><router-link to="/catalog" class="hover:text-gold-light transition">Catalogue</router-link></li>
            <li><router-link to="/faq" class="hover:text-gold-light transition">FAQ</router-link></li>
            <li><router-link to="/contact" class="hover:text-gold-light transition">Contact</router-link></li>
          </ul>
        </div>
        <div>
          <h4 class="text-gold-light font-semibold mb-4">Mon compte</h4>
          <ul class="space-y-2 text-sm">
            <li><router-link to="/orders" class="hover:text-gold-light transition">Mes commandes</router-link></li>
            <li><router-link to="/profile" class="hover:text-gold-light transition">Mon profil</router-link></li>
            <li><router-link to="/cart" class="hover:text-gold-light transition">Panier</router-link></li>
          </ul>
        </div>
        <div>
          <h4 class="text-gold-light font-semibold mb-4">Contact</h4>
          <ul class="space-y-3 text-sm">
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 mt-0.5 text-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>Lot III F 179 - Antohomadinika - Antananarivo - Madagascar</span>
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span>+261 34 66 234 95</span>
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span>goldenshopmdg@gmail.com</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="border-t border-yellow-600/20 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
        <p>&copy; {{ new Date().getFullYear() }} Golden Shop Mdg. Tous droits réservés.</p>
        <div class="flex gap-4">
          <a href="#" class="hover:text-gold-light transition">Mentions légales</a>
          <a href="#" class="hover:text-gold-light transition">CGV</a>
          <a href="#" class="hover:text-gold-light transition">Confidentialité</a>
        </div>
      </div>
    </div>
  </footer>
</template>
