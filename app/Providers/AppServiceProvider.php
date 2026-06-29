<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Module service providers to register.
     *
     * @var array<class-string<ServiceProvider>>
     */
    private const array MODULE_PROVIDERS = [
        \Modules\User\Providers\UserServiceProvider::class,
        \Modules\Order\Providers\OrderServiceProvider::class,
    ];

    public function register(): void
    {
        foreach (self::MODULE_PROVIDERS as $provider) {
            $this->app->register($provider);
        }
    }

    public function boot(): void
    {
        //
    }
}
