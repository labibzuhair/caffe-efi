<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->integer('qty')->default(1);
            $table->decimal('base_price', 15, 2)->default(0);
            $table->decimal('cogs', 15, 2)->default(0);
            $table->json('addons')->nullable(); // Menyimpan data addon (id, name, price)
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
