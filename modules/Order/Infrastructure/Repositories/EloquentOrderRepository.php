<?php

declare(strict_types=1);

namespace Modules\Order\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Modules\Shared\Domain\ValueObjects\Uuid;

final readonly class EloquentOrderRepository implements OrderRepositoryInterface
{
    private const string TABLE = 'orders_projection';

    public function findById(Uuid $id): ?array
    {
        $record = DB::table(self::TABLE)
            ->where('id', $id->value())
            ->first();

        if ($record === null) {
            return null;
        }

        $order = (array) $record;
        $order['items'] = json_decode($order['items'], true);

        return $order;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByUserId(Uuid $userId): array
    {
        return DB::table(self::TABLE)
            ->where('user_id', $userId->value())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (object $record): array {
                $order = (array) $record;
                $order['items'] = json_decode($order['items'], true);

                return $order;
            })
            ->all();
    }

    public function exists(Uuid $id): bool
    {
        return DB::table(self::TABLE)
            ->where('id', $id->value())
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
            ->map(function (object $record): array {
                $order = (array) $record;
                $order['items'] = json_decode($order['items'], true);

                return $order;
            })
            ->all();
    }

    public function count(): int
    {
        return DB::table(self::TABLE)->count();
    }
}
