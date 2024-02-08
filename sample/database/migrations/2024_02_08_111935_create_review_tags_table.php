<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('review_tags', function (Blueprint $table) {
            $table->integer('review_id', false, true);
            $table->integer('tag_id', false, true);
            $table->timestamp('created_at')->nullable();
            $table->unique(['review_id', 'tag_id'], 'UNIQUE_IDX_REVIEW_TAGS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_tags');
    }
};
