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
        Schema::create('order_details', function (Blueprint $table) {
            $table->string('order_code', 32);
            $table->integer('detail_no');
            $table->string('item_name', 100);
            $table->integer('item_price');
            $table->integer('quantity');
            $table->integer('subtotal_price');

            $table->primary(['order_code', 'detail_no']);

            $table->index('order_code');
            /** @noinspection PhpUndefinedMethodInspection */
            $table->foreign('order_code')->references('order_code')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
