<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom role sudah ada (dari bawaan sebelumnya)
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('kasir'); // Role: 'admin' atau 'kasir'
            }
            $table->string('phone')->nullable();
            $table->json('social_media')->nullable(); // Untuk menyimpan FB/IG personal staf
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'social_media']);
        });
    }
};
