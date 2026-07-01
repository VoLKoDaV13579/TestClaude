<?php

declare(strict_types=1);

namespace Modules\Order\Application\Commands;

final readonly class CreateOrderCommand
{
    /**
     * @param array<int, array{product_id: string, quantity: int, price_cents: int}> $items
     */
    public function __construct(
        public string $userId,
        public array $items,
        public string $currency = 'USD',
    ) {}
}
