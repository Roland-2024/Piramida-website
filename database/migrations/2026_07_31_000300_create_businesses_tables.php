<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('category', 30);
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->string('website_url', 2048)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('display_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['category', 'display_order']);
            $table->index(['is_featured', 'display_order']);
        });

        Schema::create('business_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->string('slug');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('address')->nullable();
            $table->text('opening_hours')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'locale']);
            $table->unique(['locale', 'slug']);
            $table->index(['locale', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_translations');
        Schema::dropIfExists('businesses');
    }
};
