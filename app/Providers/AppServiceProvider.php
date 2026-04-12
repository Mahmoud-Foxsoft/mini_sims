<?php

namespace App\Providers;

use App\Http\Services\PhoneServiceService;
use App\Repositories\Facades\SettingFacade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $repos = scandir(app_path('Repositories'));
        foreach ($repos as $repo) {
            if (str_ends_with($repo, 'Repo.php')) {
                $repoName = str_replace('Repo.php', '', $repo);
                $this->app->bind("App\\Repositories\\Interfaces\\{$repoName}Interface", "App\\Repositories\\{$repoName}Repo");
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::macro('nowPayments', function () {
            return Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => config('services.nowPayments.api_key'),
            ])->baseUrl(config('services.nowPayments.api_url'));
        });
        Http::macro('centralServer', function () {
            return Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => config('services.centralServer.api_key'),
            ])->baseUrl(config('services.centralServer.api_url'));
        });
        Passport::tokensCan([
            'admin-api' => 'Access Admin API',
            'user-api' => 'Access User API',
            'external-api' => 'Access External API',
        ]);

        View::composer(['clinic.*', 'errors.404'], function ($view) {
            $settings = SettingFacade::getCached();
            $view->with($settings);
        });

        View::composer(['clinic.home','clinic.services'], function ($view) {
            $services = PhoneServiceService::getPhoneServices();
            $view->with(['services' => $services]);
        });
    }
}
