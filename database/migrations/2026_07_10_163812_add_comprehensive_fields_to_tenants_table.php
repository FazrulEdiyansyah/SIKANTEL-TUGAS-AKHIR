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
        Schema::table('tenants', function (Blueprint $table) {
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('nik')->nullable();
            $table->text('address')->nullable();
            $table->string('ktp_document')->nullable();
            $table->string('contract_document')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'contract_start_date',
                'contract_end_date',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
                'nik',
                'address',
                'ktp_document',
                'contract_document'
            ]);
        });
    }
};
