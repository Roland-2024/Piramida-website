<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leasing_units', function (Blueprint $table): void {
            $table->id();
            $table->string('floor');
            $table->string('code')->unique();
            $table->string('svg_id')->unique();
            $table->unsignedInteger('display_order');
        });

        // Fixed inventory from the supplied SVGs; A16 and D2 are not in the plans.
        $floors = [
            'ground' => ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10', 'A11', 'A12', 'A13', 'A14', 'A15', 'A17', 'A18', 'A19', 'A20'],
            'third' => ['D1', 'D3'],
            'roof' => ['E1', 'E2'],
            'exterior' => ['BE1', 'BE1/1', 'BE2', 'BE3', 'BE4', 'BE5', 'BE6', 'BE7', 'BE8', 'BE9', 'BE10', 'BE11', 'BE12', 'BE13', 'BE14', 'BE15', 'BE16'],
        ];
        $order = 0;
        foreach ($floors as $floor => $codes) {
            foreach ($codes as $code) {
                DB::table('leasing_units')->insert([
                    'floor' => $floor,
                    'code' => $code,
                    'svg_id' => 'unit-'.str_replace('/', '-', $code),
                    'display_order' => $order++,
                ]);
            }
        }

        Schema::table('spaces', function (Blueprint $table): void {
            $table->foreignId('leasing_unit_id')->nullable()->unique()->constrained('leasing_units')->restrictOnDelete();
            $table->boolean('is_available')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('leasing_unit_id');
            $table->dropColumn('is_available');
        });
        Schema::dropIfExists('leasing_units');
    }
};
