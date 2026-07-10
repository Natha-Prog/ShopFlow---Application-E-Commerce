<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@golden-shop.mg'],
            [
                'name' => 'Admin Golden-Shop',
                'password' => Hash::make('Admin@2024'),
                'role' => 'admin',
                'phone' => '032 03 100 00',
            ]
        );

        User::updateOrCreate(
            ['email' => 'client@golden-shop.mg'],
            [
                'name' => 'Client Test',
                'password' => Hash::make('Client@2024'),
                'role' => 'client',
                'phone' => '034 75 100 00',
            ]
        );
    }
}
