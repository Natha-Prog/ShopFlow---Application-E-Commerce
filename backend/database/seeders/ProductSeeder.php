<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Électronique
            ['Casque Audio Premium', 'electronique', 450000, 15, 'photo-1505740420928-5e560c06d30e', 'Sony', 'Noir', 'Unique'],
            ['Montre Connectée', 'electronique', 650000, 20, 'photo-1523275335684-37898b6baf30', 'Apple', 'Argent', '42mm'],
            ['Enceinte Bluetooth', 'electronique', 320000, 18, 'photo-1608043152269-423dbba4e7e1', 'JBL', 'Bleu', 'M'],
            ['Clavier Mécanique', 'electronique', 280000, 12, 'photo-1587829741301-dc798b91add1', 'Logitech', 'Noir', 'Standard'],
            ['Écouteurs Sans Fil', 'electronique', 180000, 25, 'photo-1590658268037-6bf12165a8df', 'Samsung', 'Blanc', 'Unique'],
            ['Tablette 10 pouces', 'electronique', 1200000, 8, 'photo-1544244015-0df4b3ffc6b0', 'Samsung', 'Gris', '10"'],
            ['Smartphone Android', 'electronique', 2500000, 10, 'photo-1511707171634-5f897ff02aa9', 'Samsung', 'Noir', '128GB'],
            ['Laptop Portable', 'electronique', 3500000, 5, 'photo-1496181133206-80ce9b88a853', 'HP', 'Gris', '15.6"'],
            ['Caméra Numérique', 'electronique', 1800000, 7, 'photo-1516035069371-29a1d246cc32', 'Canon', 'Noir', '24MP'],
            ['Disque Dur Externe', 'electronique', 150000, 30, 'photo-1527835635083-c3a0c1e8c8d0', 'Seagate', 'Noir', '1TB'],
            
            // Mode
            ['Sac à Dos Urbain', 'mode', 180000, 30, 'photo-1553062407-98eeb64c6a62', 'Samsonite', 'Gris', 'L'],
            ['Lunettes de Soleil', 'mode', 120000, 25, 'photo-1572635196237-14b3f281503f', 'Ray-Ban', 'Noir', 'Unique'],
            ['Veste en Cuir', 'mode', 450000, 10, 'photo-1551028719-00167b16eac5', 'Zara', 'Marron', 'M'],
            ['Sneakers Running', 'mode', 280000, 22, 'photo-1542291026-7eec264c27ff', 'Nike', 'Rouge', '42'],
            ['Robe d\'Été', 'mode', 150000, 18, 'photo-1595777453418-0c5d2c6c6e0e', 'H&M', 'Floral', 'S'],
            ['Jean Slim Fit', 'mode', 85000, 35, 'photo-1542272454315-4c342d8c2b9c', 'Levi\'s', 'Bleu', '32'],
            ['T-shirt Premium', 'mode', 45000, 50, 'photo-1521572163474-6864f9cf17ab', 'Uniqlo', 'Blanc', 'M'],
            ['Montre Classique', 'mode', 350000, 15, 'photo-1524592094714-0f0654e814ad', 'Fossil', 'Or', '42mm'],
            ['Ceinture en Cuir', 'mode', 55000, 40, 'photo-1553062407-98eeb64c6a62', 'Hermès', 'Marron', 'Unique'],
            ['Écharpe en Laine', 'mode', 75000, 25, 'photo-1520975984719-7b3e9a2d0f0d', 'Burberry', 'Gris', 'Unique'],
            
            // Maison
            ['Cafetière Italienne', 'maison', 85000, 40, 'photo-1517668808822-9ebb02f2a0e6', 'Bialetti', 'Argent', '3 tasses'],
            ['Lampe de Bureau LED', 'maison', 95000, 22, 'photo-1507473885765-e6ed057f782c', 'Philips', 'Blanc', 'Unique'],
            ['Coussin Déco', 'maison', 45000, 35, 'photo-1584100936595-c065b4f2a3b0', 'IKEA', 'Beige', '40x40'],
            ['Set de Couteaux', 'maison', 180000, 14, 'photo-1593618998160-e34014e675e0', 'WMF', 'Acier', '5 pièces'],
            ['Grille-pain', 'maison', 65000, 28, 'photo-1570222094114-d0165cc9b1de', 'Bosch', 'Noir', 'Unique'],
            ['Aspirateur Robot', 'maison', 850000, 8, 'photo-1558618666-fcd25c85cd64', 'iRobot', 'Blanc', 'Unique'],
            ['Plante Artificielle', 'maison', 35000, 45, 'photo-1459411552884-841db9b3cc2a', 'IKEA', 'Vert', 'Moyenne'],
            ['Miroir Décoratif', 'maison', 120000, 18, 'photo-1618220179428-22790b461013', 'Maisons du Monde', 'Doré', '80cm'],
            ['Tapis Salon', 'maison', 250000, 12, 'photo-1576500295576-59c7ad9ec8d6', 'Maisons du Monde', 'Beige', '200x150'],
            ['Organisateur Cuisine', 'maison', 55000, 30, 'photo-1556909114-e6f2f8cc9b1d', 'IKEA', 'Blanc', '3 niveaux'],
            
            // Sport
            ['Tapis de Yoga', 'sport', 65000, 28, 'photo-1601925260368-ae2f83cf8b7f', 'Decathlon', 'Violet', 'Standard'],
            ['Haltères Ajustables', 'sport', 450000, 6, 'photo-1534438327276-14e5300c3a48', 'Domyos', 'Noir', '20kg'],
            ['Ballon de Football', 'sport', 45000, 40, 'photo-1614632537428-475e84f2e010', 'Adidas', 'Blanc', '5'],
            ['Vélo de Ville', 'sport', 1200000, 5, 'photo-1485965120183-e224f799d6b5', 'Decathlon', 'Rouge', 'Unique'],
            ['Raquette de Tennis', 'sport', 180000, 15, 'photo-1622279457366-5e1b7d4b3b1e', 'Wilson', 'Rouge', 'Unique'],
            ['Maillot de Sport', 'sport', 35000, 50, 'photo-1521572163474-6864f9cf17ab', 'Nike', 'Noir', 'M'],
            ['Chaussures de Sport', 'sport', 220000, 20, 'photo-1542291026-7eec264c27ff', 'Adidas', 'Blanc', '42'],
            ['Gourde Isotherme', 'sport', 25000, 60, 'photo-1602143407151-7111542de6e8', 'Camelbak', 'Bleu', '750ml'],
            ['Sac de Sport', 'sport', 85000, 25, 'photo-1553062407-98eeb64c6a62', 'Nike', 'Noir', 'L'],
            ['Montre GPS Sport', 'sport', 550000, 10, 'photo-1523275335684-37898b6baf30', 'Garmin', 'Noir', 'Unique'],
            
            // Beauté
            ['Crème Hydratante', 'beaute', 65000, 50, 'photo-1556228720-195a672e8a03', 'Nivea', 'Blanc', '50ml'],
            ['Parfum Eau de Toilette', 'beaute', 180000, 16, 'photo-1541643600914-78b084683601', 'Chanel', 'Transparent', '100ml'],
            ['Rouge à Lèvres', 'beaute', 45000, 40, 'photo-1586495777744-4413f21062fa', 'MAC', 'Rouge', 'Unique'],
            ['Sérum Visage', 'beaute', 120000, 25, 'photo-1620916566398-39f1143ab7be', 'La Roche-Posay', 'Transparent', '30ml'],
            ['Maquillage Palette', 'beaute', 85000, 30, 'photo-1512496015851-a90fb38ba796', 'Sephora', 'Multicolore', '12 ombres'],
            ['Brosse à Dents Électrique', 'beaute', 150000, 20, 'photo-1559599103-5b5f0e9c8b0e', 'Oral-B', 'Blanc', 'Unique'],
            ['Shampoing Premium', 'beaute', 35000, 55, 'photo-1535585160429-8d1cd1e9d9e0', 'Kérastase', 'Transparent', '250ml'],
            ['Crème Solaire', 'beaute', 45000, 45, 'photo-1556228578-0d38b6c8b3c8', 'La Roche-Posay', 'Blanc', '50ml'],
            ['Parfum Homme', 'beaute', 220000, 18, 'photo-1541643600914-78b084683601', 'Dior', 'Transparent', '100ml'],
            ['Kit Soins Visage', 'beaute', 280000, 12, 'photo-1596462502278-27bfdd4013b0', 'L\'Oréal', 'Multicolore', '5 produits'],
            
            // Livres
            ['Roman Bestseller', 'livres', 35000, 45, 'photo-1544947950-fa07a98d237f', 'Gallimard', 'Multicolore', 'Poche'],
            ['Guide de Cuisine', 'livres', 55000, 20, 'photo-1495446815901-a7297e633e8d', 'Larousse', 'Rouge', 'Relié'],
            ['Manga Populaire', 'livres', 25000, 50, 'photo-1541963463532-d68292c34b19', 'Kurokawa', 'Multicolore', 'Tome 1'],
            ['BD Classique', 'livres', 18000, 40, 'photo-1519682337056-a9d269497990', 'Dupuis', 'Multicolore', 'Album'],
            ['Livre Développement Personnel', 'livres', 45000, 30, 'photo-1544716306-992b8ddad2d1', 'Eyrolles', 'Bleu', 'Broché'],
            ['Dictionnaire Français', 'livres', 85000, 15, 'photo-1516979187457-637abb4f9353', 'Larousse', 'Rouge', 'Relié'],
            ['Livre Enfant', 'livres', 15000, 55, 'photo-1544947950-fa07a98d237f', 'Gallimard', 'Multicolore', 'Album'],
            ['Guide Touristique', 'livres', 45000, 25, 'photo-1469854523086-cc02fe5d8800', 'Lonely Planet', 'Multicolore', 'Broché'],
            ['Livre Science Fiction', 'livres', 28000, 35, 'photo-1532012197267-da84d127e765', 'Folio', 'Multicolore', 'Poche'],
            ['Cahier de Coloriage', 'livres', 12000, 60, 'photo-1519682337056-a9d269497990', 'Hachette', 'Multicolore', 'Unique'],
        ];

        foreach ($products as [$name, $catSlug, $price, $stock, $img, $brand, $color, $size]) {
            $category = Category::where('slug', $catSlug)->first();
            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $category->id,
                    'name' => $name,
                    'description' => "Description détaillée pour {$name}. Produit de qualité premium.",
                    'price' => $price,
                    'stock' => $stock,
                    'image' => "https://images.unsplash.com/{$img}?w=400&h=400&fit=crop",
                    'attributes' => ['brand' => $brand, 'color' => $color, 'size' => $size],
                ]
            );
        }
    }
}
