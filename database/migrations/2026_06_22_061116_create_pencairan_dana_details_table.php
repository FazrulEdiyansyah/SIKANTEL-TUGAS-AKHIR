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
        Schema::create('pencairan_dana_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pencairan_dana_id')->constrained('pencairan_danas')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->decimal('total_price', 12, 2);
            $table->decimal('dana_tenant', 12, 2);
            $table->decimal('dana_telu', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencairan_dana_details');
    }
};
