<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tabel Master Opsi (Dibuat oleh Admin di pengaturan Menu)
        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->string('category'); // Contoh: 'Level Pedas', 'Pilihan Susu', 'Extra Topping'
            $table->string('name'); // Contoh: 'Pedas Sedang', 'Oat Milk', 'Extra Shot Espresso'

            // Tambahan Harga & Modal (Bisa 0 jika seperti Level Pedas)
            $table->integer('additional_price')->default(0);
            $table->integer('additional_cogs')->default(0); // Modal untuk opsi ini (Penting untuk Laba Rugi!)

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Pilihan Pelanggan (Disimpan saat transaksi terjadi)
        Schema::create('order_item_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_addon_id')->nullable()->constrained()->nullOnDelete();

            // KITA SIMPAN SNAPSHOT (Foto kopi data)
            // Agar jika Admin menghapus/mengubah harga Oat Milk besok,
            // tagihan pelanggan hari ini tidak ikut berubah!
            $table->string('addon_name'); // Format: "Pilihan Susu: Oat Milk"
            $table->integer('addon_price');
            $table->integer('addon_cogs');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_addons');
        Schema::dropIfExists('product_addons');
    }
};
