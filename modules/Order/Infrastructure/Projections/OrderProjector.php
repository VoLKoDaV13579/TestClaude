<?php

declare(strict_types=1);

namespace Modules\Order\Infrastructure\Projections;

use Illuminate\Support\Facades\DB;
use Modules\Order\Domain\Events\OrderCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class OrderProjector extends Projector
{
    public function onOrderCreated(OrderCreated $event): void
    {
        DB::table('orders_projection')->insert([
            'id' => $event->orderId,
            'user_id' => $event->userId,
            'items' => json_encode($event->items),
            'total_cents' => $event->totalCents,
            'currency' => $event->currency,
            'created_at' => $event->createdAt,
            'updated_at' => $event->createdAt,
        ]);
    }

    public function resetState(): void
    {
        DB::table('orders_projection')->truncate();
    }
}
