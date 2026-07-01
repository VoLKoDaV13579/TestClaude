<?php

declare(strict_types=1);

namespace Modules\User\Domain\Repositories;

use Modules\Shared\Domain\ValueObjects\Uuid;

interface UserRepositoryInterface
{
    public function findById(Uuid $id): ?array;

    public function findByEmail(string $email): ?array;

    public function exists(Uuid $id): bool;

    public function emailExists(string $email): bool;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(int $page = 1, int $perPage = 15): array;

    public function count(): int;
}
