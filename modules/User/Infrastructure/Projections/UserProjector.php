<?php

declare(strict_types=1);

namespace Modules\User\Infrastructure\Projections;

use Illuminate\Support\Facades\DB;
use Modules\User\Domain\Events\UserRegistered;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class UserProjector extends Projector
{
    public function onUserRegistered(UserRegistered $event): void
    {
        DB::table('users_projection')->insert([
            'id' => $event->userId,
            'name' => $event->name,
            'email' => $event->email,
            'created_at' => $event->registeredAt,
            'updated_at' => $event->registeredAt,
        ]);
    }

    public function resetState(): void
    {
        DB::table('users_projection')->truncate();
    }
}
