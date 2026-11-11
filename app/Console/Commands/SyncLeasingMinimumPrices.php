<?php

namespace App\Console\Commands;

use App\Enums\SpaceType;
use App\Models\Space;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncLeasingMinimumPrices extends Command
{
    protected $signature = 'leasing:sync-minimum-prices {--apply : Save prices; without this option only preview changes}';

    protected $description = 'Apply supplied ALL/m² minimums to existing leasing posts only';

    public function handle(): int
    {
        $prices = config('leasing.minimum_prices_all');
        DB::transaction(function () use ($prices): void {
            foreach (Space::query()->where('type', SpaceType::Leasing)->with('leasingUnit')->get() as $space) {
                $code = $space->leasingUnit?->code;
                if (! isset($prices[$code])) {
                    $this->warn("Skipped post {$space->id}: no supplied minimum for ".($code ?? 'unassigned unit'));

                    continue;
                }
                $this->line("{$code}: {$space->price_from} {$space->currency} -> {$prices[$code]} ALL/m²");
                if ($this->option('apply')) {
                    $space->update(['price_from' => $prices[$code], 'currency' => 'ALL']);
                }
            }
        });
        $this->info($this->option('apply') ? 'Existing leasing prices updated.' : 'Preview only. Use --apply to save.');

        return self::SUCCESS;
    }
}
