<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('internal_name');
            $table->string('type', 40);
            $table->foreignId('primary_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('secondary_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('primary_button_url', 2048)->nullable();
            $table->string('secondary_button_url', 2048)->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('structured_data')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['page_id', 'is_active', 'display_order']);
            $table->index('type');
        });

        Schema::create('page_section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_section_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('primary_button_label')->nullable();
            $table->string('secondary_button_label')->nullable();
            $table->timestamps();

            $table->unique(['page_section_id', 'locale']);
            $table->index(['locale', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_section_translations');
        Schema::dropIfExists('page_sections');
    }
};
