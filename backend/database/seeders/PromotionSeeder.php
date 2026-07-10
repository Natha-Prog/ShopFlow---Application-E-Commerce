<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
            ['BIENVENUE10', 'percentage', 10, 0, 100, true],
            ['PROMO20', 'percentage', 20, 50, 50, true],
            ['LIVRAISON', 'fixed', 5.99, 30, null, true],
            ['ETE2024', 'percentage', 15, 100, 200, true],
        ];

        foreach ($promos as [$code, $type, $value, $min, $maxUses, $active]) {
            Promotion::updateOrCreate(
                ['code' => $code],
                [
                    'type' => $type,
                    'value' => $value,
                    'min_purchase' => $min,
                    'max_uses' => $maxUses,
                    'used_count' => 0,
                    'starts_at' => now()->subMonth(),
                    'ends_at' => now()->addYear(),
                    'is_active' => $active,
                ]
            );
        }
    }
}
