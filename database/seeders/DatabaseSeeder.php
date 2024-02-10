<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
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

        Category::create([
            "name" => "SEASON I",
            "description" => "Bagus banget ini!"
        ]);
    }
}
