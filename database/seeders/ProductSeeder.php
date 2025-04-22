<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'Indomie Goreng',
                'price' => 3500,
                'category' => 'Makanan',
                'stock' => 100,
            ],
            [
                'name' => 'Indomie Kuah',
                'price' => 3200,
                'category' => 'Makanan',
                'stock' => 100,
            ],
            [
                'name' => 'Aqua 600ml',
                'price' => 4000,
                'category' => 'Minuman',
                'stock' => 50,
            ],
            [
                'name' => 'Aqua 1.5L',
                'price' => 7000,
                'category' => 'Minuman',
                'stock' => 30,
            ],
            [
                'name' => 'Chitato Original 68gr',
                'price' => 10500,
                'category' => 'Makanan',
                'stock' => 25,
            ],
            [
                'name' => 'Teh Pucuk 350ml',
                'price' => 5000,
                'category' => 'Minuman',
                'stock' => 40,
            ],
            [
                'name' => 'Oreo Original',
                'price' => 8000,
                'category' => 'Makanan',
                'stock' => 30,
            ],
            [
                'name' => 'Pocari Sweat 500ml',
                'price' => 8500,
                'category' => 'Minuman',
                'stock' => 20,
            ],
            [
                'name' => 'Tisu Paseo',
                'price' => 12000,
                'category' => 'Perlengkapan',
                'stock' => 15,
            ],
            [
                'name' => 'Sabun Lifebuoy',
                'price' => 4500,
                'category' => 'Perlengkapan',
                'stock' => 25,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}