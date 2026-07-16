<?php

namespace App\Console\Commands;

use App\Events\ServicesUpdated;
use App\Http\Services\PhoneServiceService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('services:sync-foxsims')]
#[Description('Sync one-time services from the FoxSims plans API')]
class SyncFoxSimsServices extends Command
{
    public function handle(): int
    {
        $result = PhoneServiceService::syncFromProvider();

        if (! $result['success']) {
            $this->error($result['message']);

            return self::FAILURE;
        }

        event(new ServicesUpdated());

        $this->info("FoxSims services synced successfully. Updated {$result['count']} services.");

        return self::SUCCESS;
    }
}
