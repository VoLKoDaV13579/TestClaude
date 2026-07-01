<?php

declare(strict_types=1);

namespace Modules\User\Application\Commands;

use Modules\User\Domain\Models\User;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\User\Domain\ValueObjects\Email;
use Modules\Shared\Domain\ValueObjects\Uuid;
use RuntimeException;

final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function handle(RegisterUserCommand $command): string
    {
        $email = Email::fromString($command->email);

        if ($this->userRepository->emailExists($email->value())) {
            throw new RuntimeException("User with email {$email->value()} already exists.");
        }

        $userId = $command->userId !== null
            ? Uuid::fromString($command->userId)
            : Uuid::generate();

        $user = User::register(
            userId: $userId,
            name: $command->name,
            email: $email,
        );

        $user->persist();

        return $userId->value();
    }
}
