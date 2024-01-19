<?php

namespace Database\Seeders;

use App\Models\Publisher;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AuthorsTableSeeder::class);
        Publisher::factory(50)->create();
    }
}
