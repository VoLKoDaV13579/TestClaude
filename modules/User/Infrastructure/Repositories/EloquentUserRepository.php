<?php

declare(strict_types=1);

namespace Modules\User\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Modules\User\Domain\Repositories\UserRepositoryInterface;

final readonly class EloquentUserRepository implements UserRepositoryInterface
{
    private const string TABLE = 'users_projection';

    public function findById(Uuid $id): ?array
    {
        $record = DB::table(self::TABLE)
            ->where('id', $id->value())
            ->first();

        return $record ? (array) $record : null;
    }

    public function findByEmail(string $email): ?array
    {
        $record = DB::table(self::TABLE)
            ->where('email', $email)
            ->first();

        return $record ? (array) $record : null;
    }

    public function exists(Uuid $id): bool
    {
        return DB::table(self::TABLE)
            ->where('id', $id->value())
            ->exists();
    }

    public function emailExists(string $email): bool
    {
        return DB::table(self::TABLE)
            ->where('email', $email)
            ->exists();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(int $page = 1, int $perPage = 15): array
    {
        return DB::table(self::TABLE)
            ->orderBy('created_at', 'desc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get()
            ->map(fn (object $record): array => (array) $record)
            ->all();
    }

    public function count(): int
    {
        return DB::table(self::TABLE)->count();
    }
}
