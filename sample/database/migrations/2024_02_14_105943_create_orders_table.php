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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code', 32);
            $table->dateTime('order_date');
            $table->string('customer_name', 100);
            $table->string('customer_email', 255);
            $table->string('destination_name', 100);
            $table->string('destination_zip', 10);
            $table->string('destination_prefecture', 10);
            $table->string('destination_address', 100);
            $table->string('destination_tel', 20);
            $table->integer('total_quantity');
            $table->integer('total_price');
            $table->timestamps();

            $table->unique('order_code');
            $table->index('order_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
