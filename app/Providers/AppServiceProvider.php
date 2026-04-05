<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
                $this->app->bind(AdminAuthInterface::class, AdminAuthRepository::class);
        $this->app->bind(UserAuthInterface::class, UserAuthRepository::class);
        $this->app->bind(PaymentInterface::class, PaymentRepo::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(AdminInterface::class, AdminRepository::class);
        $this->app->bind(SettingInterface::class, SettingRepo::class);
        $this->app->bind(ContactInterface::class, ContactRepo::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
               Passport::tokensCan([
            'admin-api' => 'Access Admin API',
            'user-api' => 'Access User API',
        ]);
    }
}
