<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        for ($number = 1; $number <= 12; $number++) {
            DB::table('leasing_units')->insert([
                'floor' => 'minus-one',
                'code' => 'U'.$number,
                'svg_id' => 'unit-U'.$number,
                'display_order' => 40 + $number,
            ]);
        }
    }

    public function down(): void
    {
        // Referenced units are protected by the existing spaces foreign key.
        DB::table('leasing_units')->where('floor', 'minus-one')->delete();
    }
};
