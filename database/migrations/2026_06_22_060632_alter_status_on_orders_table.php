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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->enum('payment_status', ['pending', 'success', 'failed', 'expired'])->default('pending')->after('total_price');
            $table->enum('order_status', ['belum_diproses', 'diproses', 'siap_diambil', 'selesai'])->default('belum_diproses')->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_status');
            $table->dropColumn('payment_status');
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending')->after('total_price');
        });
    }
};
