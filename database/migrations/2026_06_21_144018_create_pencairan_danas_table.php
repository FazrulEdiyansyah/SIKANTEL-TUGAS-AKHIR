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
        Schema::create('pencairan_danas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengelola_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('approver_name')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_penjualan', 12, 2);
            $table->decimal('dana_tenant', 12, 2);
            $table->decimal('dana_telu', 12, 2);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'proposed', 'approved', 'rejected'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencairan_danas');
    }
};
