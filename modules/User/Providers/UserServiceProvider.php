<?php

declare(strict_types=1);

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\User\Infrastructure\Projections\UserProjector;
use Modules\User\Infrastructure\Repositories\EloquentUserRepository;
use Spatie\EventSourcing\Facades\Projectionist;

final class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class,
        );
    }

    public function boot(): void
    {
        Projectionist::addProjector(UserProjector::class);

        $this->loadRoutesFrom(
            __DIR__ . '/../Presentation/Http/Routes/api.php',
        );
    }
}
