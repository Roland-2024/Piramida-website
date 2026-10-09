<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_categories', function (Blueprint $table): void {
            $table->string('slug', 30)->primary();
            $table->unsignedInteger('display_order');
        });
        foreach (['education', 'innovation', 'business', 'art_culture', 'social_spaces'] as $order => $slug) {
            DB::table('business_categories')->insert(['slug' => $slug, 'display_order' => $order]);
        }
        Schema::create('business_category', function (Blueprint $table): void {
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('category_slug', 30);
            $table->foreign('category_slug')->references('slug')->on('business_categories')->restrictOnDelete();
            $table->primary(['business_id', 'category_slug']);
            $table->index(['category_slug', 'business_id']);
        });
        // Keep the old value only for a lossless rollback of existing records.
        Schema::table('businesses', function (Blueprint $table): void {
            $table->dropIndex(['category', 'display_order']);
            $table->renameColumn('category', 'legacy_category');
        });
        Schema::table('businesses', fn (Blueprint $table) => $table->string('legacy_category', 30)->nullable()->change());

        DB::table('businesses')->orderBy('id')->chunkById(100, function ($businesses): void {
            foreach ($businesses as $business) {
                DB::table('business_category')->insert([
                    'business_id' => $business->id,
                    'category_slug' => match ($business->legacy_category) {
                        'technology' => 'innovation',
                        'art' => 'art_culture',
                        'cafe', 'restaurant', 'shop' => 'social_spaces',
                        default => 'business',
                    },
                ]);
            }
        });
    }

    public function down(): void
    {
        DB::table('businesses')->whereNull('legacy_category')->update(['legacy_category' => 'shop']);
        Schema::table('businesses', function (Blueprint $table): void {
            $table->string('legacy_category', 30)->nullable(false)->change();
            $table->renameColumn('legacy_category', 'category');
        });
        Schema::table('businesses', fn (Blueprint $table) => $table->index(['category', 'display_order']));
        Schema::dropIfExists('business_category');
        Schema::dropIfExists('business_categories');
    }
};
