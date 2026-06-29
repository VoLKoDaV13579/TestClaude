<?php

declare(strict_types=1);

namespace Modules\Order\Domain\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

final class OrderCreated extends ShouldBeStored
{
    /**
     * @param array<int, array{product_id: string, quantity: int, price_cents: int}> $items
     */
    public function __construct(
        public readonly string $orderId,
        public readonly string $userId,
        public readonly array $items,
        public readonly int $totalCents,
        public readonly string $currency,
        public readonly string $createdAt,
    ) {}
}
