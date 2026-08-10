<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->timestamp('wordpress_synced_at')->nullable()->after('display_order');
        });

        Schema::table('event_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_id')->nullable()->unique()->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('event_translations', function (Blueprint $table) {
            $table->dropUnique(['wordpress_id']);
            $table->dropColumn('wordpress_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('wordpress_synced_at');
        });
    }
};
