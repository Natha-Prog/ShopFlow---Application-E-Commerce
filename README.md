# ShopFlow - Application E-Commerce

Application web complète de gestion d'une boutique en ligne avec frontend Vue.js et backend Laravel.

## 📸 Captures d'écran

*(Ajoutez ici des captures d'écran de l'application)*

- Page d'accueil
- Catalogue de produits
- Panier
- Page de commande
- Tableau de bord admin

## 🚀 Démo en ligne

[Lien vers la démo en ligne - à ajouter après déploiement]

## 💻 Code Source

[GitHub Repository - à ajouter après création du repo]

## 🛠️ Stack Technique

### Frontend
- **Vue.js 3** - Framework JavaScript progressif
- **TypeScript** - Typage statique
- **Vite** - Build tool ultra-rapide
- **Pinia** - Gestion d'état
- **Vue Router** - Routage
- **Axios** - Client HTTP
- **TailwindCSS** - Framework CSS utilitaire

### Backend
- **Laravel 12** - Framework PHP
- **SQLite** (défaut) ou **PostgreSQL** - Base de données
- **Laravel Sanctum** - Authentification API

## ✨ Fonctionnalités

### Utilisateur
- ✅ Inscription et connexion
- ✅ Catalogue de produits avec filtrage par catégorie
- ✅ Recherche de produits
- ✅ Panier persistant
- ✅ Passage de commande
- ✅ Historique des commandes
- ✅ Suivi du statut des commandes

### Admin
- ✅ Tableau de bord avec statistiques
- ✅ Gestion CRUD des produits
- ✅ Gestion des commandes
- ✅ Mise à jour du statut des commandes
- ✅ Accès restreint par rôle

## 📦 Installation

### Prérequis
- Node.js 24+
- PHP 8.2+
- Composer
- PostgreSQL

### Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

### Comptes de test (après seed)

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@shopflow.com | password |
| Client | client@shopflow.com | password |

### Frontend (Vue.js)

```bash
cd frontend
npm install
npm run dev
```

L'application sera accessible sur `http://localhost:5173`

## 🗄️ Base de Données

### Tables principales
- **users** - Utilisateurs (clients et admins)
- **categories** - Catégories de produits
- **products** - Produits
- **cart_items** - Articles du panier
- **orders** - Commandes
- **order_items** - Articles de commande

## 🔐 Authentification

L'authentification est gérée via Laravel Sanctum avec des tokens JWT. Les routes protégées nécessitent un header Authorization:
```
Authorization: Bearer {token}
```

## 📝 Problème et Solution

### Problème
Créer une application e-commerce complète avec une séparation claire entre frontend et backend, permettant une gestion efficace des produits, paniers et commandes tout en assurant une expérience utilisateur fluide.

### Solution
- **Architecture SPA**: Utilisation de Vue.js pour une interface réactive sans rechargement de page
- **API RESTful**: Backend Laravel avec endpoints bien définis pour toutes les opérations
- **Gestion d'état centralisée**: Pinia pour gérer l'authentification, le panier et les produits
- **Base de données relationnelle**: PostgreSQL pour assurer l'intégrité des données
- **Authentification sécurisée**: Laravel Sanctum pour la protection des routes sensibles
- **Interface admin dédiée**: Espace séparé pour la gestion des produits et commandes

### Défis techniques rencontrés
1. **Synchronisation du panier**: Solution avec persistance en base de données pour éviter la perte de données
2. **Gestion des stocks**: Implémentation de vérifications de stock avant ajout au panier
3. **Routage protégé**: Middleware Vue Router pour les routes authentifiées et admin
4. **État global**: Pinia stores pour partager l'état entre composants

## 🎯 Améliorations futures

- [ ] Intégration Stripe pour les paiements (architecture préparée)
- [ ] Notifications email
- [ ] Mode sombre
- [ ] Tests unitaires et E2E
- [ ] Docker pour le déploiement

## 👤 Auteur

Nata-Dev - Développeur Backend et Frontend

## 📄 Licence

MIT License
