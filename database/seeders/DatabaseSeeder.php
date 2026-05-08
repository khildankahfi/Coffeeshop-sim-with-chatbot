<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key check sementara saat truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Product::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $categories = [
            ['name' => 'Espresso Based', 'slug' => 'espresso'],
            ['name' => 'Manual Brew',    'slug' => 'manual-brew'],
            ['name' => 'Non Coffee',     'slug' => 'non-coffee'],
            ['name' => 'Food',           'slug' => 'food'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        $products = [
            ['category_id' => 1, 'name' => 'Americano',    'price' => 25000],
            ['category_id' => 1, 'name' => 'Cappuccino',   'price' => 28000],
            ['category_id' => 1, 'name' => 'Latte',        'price' => 30000],
            ['category_id' => 2, 'name' => 'V60 Ethiopia', 'price' => 35000],
            ['category_id' => 2, 'name' => 'Aeropress',    'price' => 32000],
            ['category_id' => 3, 'name' => 'Matcha Latte', 'price' => 28000],
            ['category_id' => 3, 'name' => 'Coklat Panas', 'price' => 22000],
            ['category_id' => 4, 'name' => 'Croissant',    'price' => 20000],
            ['category_id' => 4, 'name' => 'Banana Bread', 'price' => 18000],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}