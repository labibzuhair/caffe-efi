<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengubah tipe kolom menjadi TEXT agar bisa menampung token panjang
            $table->text('provider_token')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengembalikan ke STRING (VARCHAR) jika di-rollback
            $table->string('provider_token')->nullable()->change();
        });
    }
};
