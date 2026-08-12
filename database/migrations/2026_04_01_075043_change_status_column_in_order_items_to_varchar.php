<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE order_items MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE order_items MODIFY COLUMN status ENUM('pending', 'cooking', 'ready_to_serve', 'served', 'completed') DEFAULT 'pending'");
    }
};
