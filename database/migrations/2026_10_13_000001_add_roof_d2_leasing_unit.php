<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('leasing_units')->insert([
            'floor' => 'roof',
            'code' => 'D2',
            'svg_id' => 'unit-D2',
            'display_order' => DB::table('leasing_units')->max('display_order') + 1,
        ]);
    }

    public function down(): void
    {
        // The spaces foreign key prevents removing an assigned unit.
        DB::table('leasing_units')->where('floor', 'roof')->where('code', 'D2')->delete();
    }
};
