<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('cash_received')->nullable()->after('payment_method')
                ->comment('Jumlah uang yang diberikan pelanggan saat bayar Cash');

            $table->integer('change_amount')->nullable()->after('cash_received')
                ->comment('Jumlah uang kembalian yang diberikan kasir ke pelanggan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cash_received', 'change_amount']);
        });
    }
};
