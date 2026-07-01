<?php

declare(strict_types=1);

namespace Modules\User\Domain\Models;

use Modules\User\Domain\Events\UserRegistered;
use Modules\User\Domain\ValueObjects\Email;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

final class User extends AggregateRoot
{
    private string $name;
    private Email $email;
    private string $registeredAt;

    public static function register(
        Uuid $userId,
        string $name,
        Email $email,
    ): self {
        $user = new self();
        $user->loadUuid($userId->value());

        $user->recordThat(new UserRegistered(
            userId: $userId->value(),
            name: $name,
            email: $email->value(),
            registeredAt: now()->toIso8601String(),
        ));

        return $user;
    }

    protected function applyUserRegistered(UserRegistered $event): void
    {
        $this->name = $event->name;
        $this->email = Email::fromString($event->email);
        $this->registeredAt = $event->registeredAt;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
