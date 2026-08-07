<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kita buat password menjadi nullable karena login via Google/GitHub tidak butuh password lokal
            $table->string('password')->nullable()->change();

            // Kolom untuk menampung data dari Social Provider
            $table->string('provider')->nullable()->after('remember_token'); // misal: 'google' atau 'github'
            $table->string('provider_id')->nullable()->after('provider'); // ID unik dari Google/GitHub
            $table->string('provider_token')->nullable()->after('provider_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
            $table->dropColumn(['provider', 'provider_id', 'provider_token']);
        });
    }
};
