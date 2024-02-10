<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'name' => 'Bagaskara',
            'email' => 'bagaskara148@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('bagas123'),
            'isAdmin' => true
        ]);

        \App\Models\User::create([
            'name' => 'Bagaskara',
            'email' => 'bagaskara_dwi_putra@teknokrat.ac.id',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('bagas123'),
            'isAdmin' => false
        ]);

        Category::create([
            "name" => "SEASON I",
            "description" => "Bagus banget ini!"
        ]);

        Product::create([
            "name" => "The Doll",
            "description" => "Bagus banget barangnya",
            "category_id" => 1,
            "image_path" => "1707489377_Who's_Control_My_Brain.png",
            "price" => 300000
        ]);

        ProductGallery::create([
            "product_id" => 1,
            "image_path" => "1707489377_Who's_Control_My_Brain.png"
        ]);

        ProductVariant::create([
            "product_id" => 1,
            "size" => "XL",
            "stock" => 30 
        ]);
    }
}
