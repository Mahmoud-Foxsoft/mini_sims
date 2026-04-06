<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Repositories\Facades\TransactionFacade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'clean_email' => 'test@example.com',
        ]);
        for ($i = 0; $i < 10; $i++) {
            TransactionFacade::createCredit(
                User::first(),
                random_int(1000, 10000),
                'Test Credit' . $i,
            );

            TransactionFacade::createDebit(
                User::first(),
                1000,
                'Test Debit' . $i,
            );
        }
    }
}
