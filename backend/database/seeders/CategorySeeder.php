<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Électronique', 'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400'],
            ['name' => 'Mode', 'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400'],
            ['name' => 'Maison', 'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400'],
            ['name' => 'Sport', 'image' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba7951?w=400'],
            ['name' => 'Beauté', 'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdd403348?w=400'],
            ['name' => 'Livres', 'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'image' => $cat['image']]
            );
        }
    }
}
