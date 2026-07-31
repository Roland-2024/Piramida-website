<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('type', 30);
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->decimal('area_sqm', 10, 2)->nullable();
            $table->decimal('price_from', 10, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->string('booking_mode', 20)->default('internal');
            $table->string('external_url', 2048)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('display_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['type', 'display_order']);
            $table->index(['is_featured', 'display_order']);
        });

        Schema::create('space_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->string('slug');
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('features')->nullable();
            $table->string('location')->nullable();
            $table->string('price_label')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();

            $table->unique(['space_id', 'locale']);
            $table->unique(['locale', 'slug']);
            $table->index(['locale', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('space_translations');
        Schema::dropIfExists('spaces');
    }
};
