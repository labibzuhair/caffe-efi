<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            // Mengubah tipe kolom status dari ENUM menjadi String biasa
            $table->string('status')->default('available')->change();
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            // Jika di-rollback, kita tidak perlu melakukan apa-apa,
            // string sudah cukup aman menampung data enum sebelumnya.
        });
    }
};
