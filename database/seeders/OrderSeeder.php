<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'user_id' => 1,
                'product_name' => 'Iphone 16',
                'total_amount' => 100650,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 1,
                'product_name' => 'Lenovo Laptop',
                'total_amount' => 80050,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 2,
                'product_name' => 'HP Laptop',
                'total_amount' => 75000,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 3,
                'product_name' => 'Samsung Smartphone',
                'total_amount' => 85000,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 5,
                'product_name' => 'Walton Fridge',
                'total_amount' => 56000,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('orders')->insert($orders);
    }
}
