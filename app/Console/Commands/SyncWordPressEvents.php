<?php

namespace App\Console\Commands;

use App\Actions\SynchronizeWordPressEvents;
use Illuminate\Console\Command;
use Throwable;

class SyncWordPressEvents extends Command
{
    protected $signature = 'events:sync-wordpress';

    protected $description = 'Synchronize bilingual events from the configured WordPress API';

    public function handle(SynchronizeWordPressEvents $synchronizer): int
    {
        try {
            $result = $synchronizer->handle();
        } catch (Throwable $exception) {
            report($exception);
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("WordPress events synchronized: {$result['created']} created, {$result['updated']} updated, {$result['drafted']} drafted, {$result['skipped']} skipped.");

        foreach ($result['warnings'] as $warning) {
            $this->warn($warning);
        }

        return $result['skipped'] === 0 ? self::SUCCESS : self::FAILURE;
    }
}
