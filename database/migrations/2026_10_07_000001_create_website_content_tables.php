<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_texts', function (Blueprint $table) {
            $table->string('key', 190);
            $table->string('locale', 2);
            $table->text('text');
            $table->primary(['key', 'locale']);
        });
        Schema::create('website_images', function (Blueprint $table) {
            $table->string('key', 40)->primary();
            $table->foreignId('media_id')->constrained('media')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_images');
        Schema::dropIfExists('website_texts');
    }
};
