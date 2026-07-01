<?php

declare(strict_types=1);

namespace Modules\User\Domain\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

final class UserRegistered extends ShouldBeStored
{
    public function __construct(
        public readonly string $userId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $registeredAt,
    ) {}
}
