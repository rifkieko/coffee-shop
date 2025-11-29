<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@coffee-shop.test'],
            [
                'name' => 'Administrator',
                'phone' => '0800000000',
                'role' => UserRole::Admin,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@coffee-shop.test'],
            [
                'name' => 'Sample Customer',
                'phone' => '0811111111',
                'role' => UserRole::Customer,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        foreach ([
            ['name' => 'Coffee', 'description' => 'Pilihan kopi hangat dan dingin.'],
            ['name' => 'Non Coffee', 'description' => 'Minuman non-kopi untuk semua selera.'],
            ['name' => 'Snack', 'description' => 'Makanan ringan pendamping kopi Anda.'],
        ] as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }

    }
}
