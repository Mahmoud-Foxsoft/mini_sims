<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PhoneMessage;
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

        // Admin::factory()->create([
        //     'name' => 'Test Admin',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('FoxSoft@123'), // 'FoxSoft@123',
        // ]);
        // User::factory(100)->create();
        for ($i = 0; $i < 10; $i++) {
            $user = User::inRandomOrder()->first();
            TransactionFacade::createCredit(
                $user,
                random_int(1000, 10000),
                'Test Credit' . $i,
            );

            TransactionFacade::createDebit(
                $user,
                1000,
                'Test Debit' . $i,
            );
        }
        // Order::factory(10)->create();
        // OrderItem::factory(10)->create();
        // PhoneMessage::factory(10)->create();
        // $this->call(
        //     [
        //         SettingSeeder::class,
        //     ]
        // );

        // Payment::factory(3)->create();
    }
}
