<?php

declare(strict_types=1);

namespace Modules\Order\Domain\Repositories;

use Modules\Shared\Domain\ValueObjects\Uuid;

interface OrderRepositoryInterface
{
    public function findById(Uuid $id): ?array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByUserId(Uuid $userId): array;

    public function exists(Uuid $id): bool;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(int $page = 1, int $perPage = 15): array;

    public function count(): int;
}
