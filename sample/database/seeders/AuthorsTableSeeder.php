<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('ja_JP');
        for ($i = 0; $i < 10; $i++) {
            $author = [
                'name' => $faker->name,
                'kana' => $faker->KanaName,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            \Illuminate\Support\Facades\DB::table('authors')->insert($author);
        }
    }
}
