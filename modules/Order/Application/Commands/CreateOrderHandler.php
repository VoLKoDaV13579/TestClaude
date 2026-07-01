<?php

declare(strict_types=1);

namespace Modules\Order\Application\Commands;

use Modules\Order\Domain\Models\Order;
use Modules\Order\Domain\ValueObjects\OrderId;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use RuntimeException;

final readonly class CreateOrderHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function handle(CreateOrderCommand $command): string
    {
        $userId = Uuid::fromString($command->userId);

        if (!$this->userRepository->exists($userId)) {
            throw new RuntimeException("User with ID {$command->userId} does not exist.");
        }

        if (empty($command->items)) {
            throw new RuntimeException('Order must contain at least one item.');
        }

        $orderId = OrderId::generate();

        $order = Order::create(
            orderId: $orderId,
            userId: $userId,
            items: $command->items,
            currency: $command->currency,
        );

        $order->persist();

        return $orderId->value();
    }
}
