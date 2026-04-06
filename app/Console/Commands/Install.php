<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

#[Signature('sys:install')]
#[Description('Command description')]
class Install extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment('Generating Keys...');
        Artisan::call('key:generate');
        $this->info('Keys Generated!');

        $this->comment('Refreshing Database...');
        Artisan::call('migrate:fresh');
        $this->info('Database Refreshed!');

        $this->comment('Generating Passport Keys...');
        Artisan::call('passport:keys', ['--force' => true]);
        Artisan::call('passport:client', ['--personal' => true,'--provider' => 'users','--name'=> 'Users Personal Access Client']);
        Artisan::call('passport:client', ['--personal' => true,'--provider' => 'admins','--name'=> 'Admins Personal Access Client']);
        $this->info('Passport Keys Generated!');

        $this->comment('Seeding Database...');
        Artisan::call('db:seed');
        $this->info('Database Seeded!');

        $this->comment('link storage...');
        Artisan::call('storage:link');
        $this->info('Storage Linked!');

        $this->comment('Clearing Cache...');
        Artisan::call('cache:clear');
        $this->info('Cache Cleared!');
    }
}
