<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('category', 30)->default('event')->after('featured_media_id');
            $table->string('booking_mode', 20)->default('none')->after('external_url');
            $table->boolean('is_featured')->default(false)->after('booking_mode');
            $table->unsignedInteger('capacity')->nullable()->after('is_featured');
            $table->unsignedInteger('display_order')->default(0)->after('capacity');
            $table->index(['category', 'starts_at']);
            $table->index(['is_featured', 'starts_at']);
        });

        Schema::table('event_translations', function (Blueprint $table) {
            $table->string('price_label')->nullable()->after('location');
        });

        DB::table('events')
            ->whereNotNull('external_url')
            ->where('booking_mode', 'none')
            ->update(['booking_mode' => 'external']);
    }

    public function down(): void
    {
        Schema::table('event_translations', function (Blueprint $table) {
            $table->dropColumn('price_label');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['category', 'starts_at']);
            $table->dropIndex(['is_featured', 'starts_at']);
            $table->dropColumn(['category', 'booking_mode', 'is_featured', 'capacity', 'display_order']);
        });
    }
};
