<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('expense_date'); // Tanggal pengeluaran
            $table->string('category'); // Kategori: 'Bahan Baku', 'Gaji', 'Listrik', 'Operasional'
            $table->string('description'); // Contoh: "Beli token listrik bulan Maret"
            $table->integer('amount'); // Nominal uang yang keluar
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Kasir/Admin yang mencatat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
