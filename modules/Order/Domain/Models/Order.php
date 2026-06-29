<?php

declare(strict_types=1);

namespace Modules\Order\Domain\Models;

use Modules\Order\Domain\Events\OrderCreated;
use Modules\Order\Domain\ValueObjects\OrderId;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

final class Order extends AggregateRoot
{
    private string $userId;

    /** @var array<int, array{product_id: string, quantity: int, price_cents: int}> */
    private array $items = [];

    private int $totalCents = 0;
    private string $currency = 'USD';

    /**
     * @param array<int, array{product_id: string, quantity: int, price_cents: int}> $items
     */
    public static function create(
        OrderId $orderId,
        Uuid $userId,
        array $items,
        string $currency = 'USD',
    ): self {
        $order = new self();
        $order->loadUuid($orderId->value());

        $totalCents = array_reduce(
            $items,
            static fn (int $carry, array $item): int => $carry + ($item['price_cents'] * $item['quantity']),
            0,
        );

        $order->recordThat(new OrderCreated(
            orderId: $orderId->value(),
            userId: $userId->value(),
            items: $items,
            totalCents: $totalCents,
            currency: $currency,
            createdAt: now()->toIso8601String(),
        ));

        return $order;
    }

    protected function applyOrderCreated(OrderCreated $event): void
    {
        $this->userId = $event->userId;
        $this->items = $event->items;
        $this->totalCents = $event->totalCents;
        $this->currency = $event->currency;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function totalCents(): int
    {
        return $this->totalCents;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    /**
     * @return array<int, array{product_id: string, quantity: int, price_cents: int}>
     */
    public function items(): array
    {
        return $this->items;
    }
}
