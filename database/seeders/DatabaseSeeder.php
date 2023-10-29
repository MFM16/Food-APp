<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(1)->create();

        // \App\Models\Product::factory(10)->create([
        //     'name' => 'Test Product',
        //     'stock' => '10',
        //     'price' => '10000',
        //     'photo' => 'product-img/login-office.jpeg',
        //     'created_at' => date('Y-m-d'),
        //     'updated_at' => date('Y-m-d')
        // ]);

        \App\Models\Cost::factory(1)->create([
            'distance' => 1000,
            'cost' => 10000,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        \App\Models\Profile::factory(1)->create([
            'user_id' => 1,
            'name' => 'Farhan',
            'phone_number' => '087888844153',
            'address' => 'Jl. Lada 3',
            'village' => 'Ragajaya',
            'District' => 'Bojonggede',
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

    }
}
