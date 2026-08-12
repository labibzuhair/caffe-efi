<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->string('category');
            $table->string('name');

            $table->integer('additional_price')->default(0);
            $table->integer('additional_cogs')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('order_item_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_addon_id')->nullable()->constrained()->nullOnDelete();


            $table->string('addon_name');
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
