<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\User;
use App\Services\DeepSeekService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return match (true) {
                $user instanceof User => 'www.africtv.com.ng/resetpassword' . '?token=' . $token . '&email=' . urlencode($user->email),
                default => throw new \Exception("Invalid user type"),
            };
        });

        $this->app->bind(DeepSeekService::class, function ($app) {
            return new DeepSeekService();
        });
    }
}
