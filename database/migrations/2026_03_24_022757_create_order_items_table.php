<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->foreignId('session_customer_id')->nullable()->constrained()->nullOnDelete();

            $table->integer('qty');
            $table->integer('price_at_order');
            $table->string('notes')->nullable();

            $table->enum('status', ['pending', 'cooking', 'ready_to_serve', 'served', 'cancelled'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
