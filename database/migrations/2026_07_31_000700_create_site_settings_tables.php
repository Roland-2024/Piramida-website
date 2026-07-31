<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('notification_email')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('facebook_url', 2048)->nullable();
            $table->string('instagram_url', 2048)->nullable();
            $table->string('linkedin_url', 2048)->nullable();
            $table->timestamps();
        });

        Schema::create('site_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_setting_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('address')->nullable();
            $table->text('opening_hours')->nullable();
            $table->text('footer_text')->nullable();
            $table->timestamps();

            $table->unique(['site_setting_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_setting_translations');
        Schema::dropIfExists('site_settings');
    }
};
