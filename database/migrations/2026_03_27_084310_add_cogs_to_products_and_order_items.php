<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cogs', 10, 2)->default(0)->after('price')->comment('Harga Pokok Penjualan / Modal Bahan Baku');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('cogs_at_order')->default(0)->after('price_at_order')->comment('Modal saat pesanan dibuat');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('cogs');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('cogs_at_order');
        });
    }
};
