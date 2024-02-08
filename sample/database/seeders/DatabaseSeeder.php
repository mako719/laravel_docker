<?php

namespace Database\Seeders;

use App\Models\Publisher;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AuthorsTableSeeder::class);
        Publisher::factory(50)->create();

        // 外部キー制約追加
        Schema::table(
            'reviews',
            function (Blueprint $table) {
                $table->foreign('user_id')->references('id')
                    ->on('users')->onDelete('cascade')->onUpdate('cascade');
            }
        );
        Schema::table(
            'review_tags',
            function (Blueprint $table) {
                $table->foreign('review_id')->references('id')
                    ->on('reviews')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('tag_id')->references('id')
                    ->on('tags')->onDelete('cascade')->onUpdate('cascade');
            }
        );
        $this->call(
            [
                UserSeeder::class
            ]
        );
    }
}
