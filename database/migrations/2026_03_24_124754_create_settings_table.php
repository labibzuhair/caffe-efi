<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // Data Utama
            $table->string('store_name')->default('CaffePOS');
            $table->decimal('tax_percentage', 5, 2)->default(0);

            // Data Tambahan (Kontak, SEO, dll)
            $table->string('logo')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('address')->nullable();
            $table->json('social_media')->nullable();
            $table->string('seo_thumbnail')->nullable();
            $table->string('footer_text')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
