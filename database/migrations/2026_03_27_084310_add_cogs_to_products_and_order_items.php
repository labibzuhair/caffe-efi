<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tambah HPP di Master Produk
        Schema::table('products', function (Blueprint $table) {
            // Kita pakai decimal agar seragam dengan kolom 'price' milikmu
            $table->decimal('cogs', 10, 2)->default(0)->after('price')->comment('Harga Pokok Penjualan / Modal Bahan Baku');
        });

        // 2. Tambah HPP Historis di Detail Pesanan
        Schema::table('order_items', function (Blueprint $table) {
            // Kita pakai integer agar seragam dengan kolom 'price_at_order' milikmu
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
