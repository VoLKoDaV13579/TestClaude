<?php

declare(strict_types=1);

namespace Modules\Order\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Modules\Order\Infrastructure\Projections\OrderProjector;
use Modules\Order\Infrastructure\Repositories\EloquentOrderRepository;
use Spatie\EventSourcing\Facades\Projectionist;

final class OrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            OrderRepositoryInterface::class,
            EloquentOrderRepository::class,
        );
    }

    public function boot(): void
    {
        Projectionist::addProjector(OrderProjector::class);

        $this->loadRoutesFrom(
            __DIR__ . '/../Presentation/Http/Routes/api.php',
        );
    }
}
