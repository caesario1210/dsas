<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['product_name' => 'Product A', 'product_code' => 'PRD-A', 'category' => 'Electronics', 'base_price' => 5000000],
            ['product_name' => 'Product B', 'product_code' => 'PRD-B', 'category' => 'Electronics', 'base_price' => 3000000],
            ['product_name' => 'Product C', 'product_code' => 'PRD-C', 'category' => 'Furniture', 'base_price' => 2000000],
            ['product_name' => 'Product D', 'product_code' => 'PRD-D', 'category' => 'Furniture', 'base_price' => 1500000],
            ['product_name' => 'Product E', 'product_code' => 'PRD-E', 'category' => 'Appliances', 'base_price' => 4000000],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
