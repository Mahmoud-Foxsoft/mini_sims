<?php

namespace App\Console\Commands;

use App\Actions\CacheCounterAction;
use App\Events\PhoneNumbersRefunded;
use App\Models\OrderItem;
use App\Models\User;
use App\Repositories\Facades\TransactionFacade;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('expire:numbers')]
#[Description('Expire phone numbers that are not used for a long time')]
class ExpirePhoneNumber extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {

        $threshold = now()->subMinutes((int) config('app.phone_number_timeout',15));

        DB::transaction(function () use ($threshold) {

            $refunds = DB::table('order_items')
                ->select('user_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(price_cents) as total'))
                ->where('status', 'pending')
                ->where('updated_at', '<=', $threshold)
                ->groupBy('user_id')
                ->lockForUpdate()
                ->get();

            if ($refunds->isEmpty()) {
                return;
            }

            DB::table('order_items')
                ->where('status', 'pending')
                ->where('updated_at', '<=', $threshold)
                ->update([
                    'status' => 'timeout_refunded',
                    'updated_at' => now()
                ]);

            foreach ($refunds as $refund) {
                $s = $refund->count > 1 ? 's' : '';
                TransactionFacade::createCredit(
                    User::find($refund->user_id),
                    $refund->total,
                    "Refund for {$refund->count} expired phone number{$s}"
                );

                CacheCounterAction::execute(
                    'pending_numbers_' . $refund->user_id,
                    $refund->count,
                    'decrement'
                );
                event(new PhoneNumbersRefunded($refund));
            }
        });
    }
}
