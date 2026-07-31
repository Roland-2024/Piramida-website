<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->string('video_url', 2048)->nullable()->after('secondary_media_id');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('x_url', 2048)->nullable()->after('facebook_url');
            $table->string('map_url', 2048)->nullable()->after('linkedin_url');
        });
    }

    public function down(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['x_url', 'map_url']);
        });
    }
};
