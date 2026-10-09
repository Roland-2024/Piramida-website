<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_translations', function (Blueprint $table) {
            $table->string('street_address')->nullable();
            $table->string('address_locality')->nullable();
            $table->string('postal_code', 32)->nullable();
            $table->string('address_country', 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('event_translations', fn (Blueprint $table) => $table->dropColumn(['street_address', 'address_locality', 'postal_code', 'address_country']));
    }
};
