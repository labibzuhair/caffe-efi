<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mengubah kolom status menjadi VARCHAR agar bebas menerima kata status apapun
        DB::statement("ALTER TABLE order_items MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opsional: Kembalikan ke ENUM jika di-rollback
        DB::statement("ALTER TABLE order_items MODIFY COLUMN status ENUM('pending', 'cooking', 'ready_to_serve', 'served', 'completed') DEFAULT 'pending'");
    }
};
