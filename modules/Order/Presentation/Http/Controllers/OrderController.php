<?php

declare(strict_types=1);

namespace Modules\Order\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Order\Application\Commands\CreateOrderCommand;
use Modules\Order\Application\Commands\CreateOrderHandler;
use Modules\Order\Domain\Repositories\OrderRepositoryInterface;
use Modules\Order\Presentation\Http\Requests\CreateOrderRequest;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class OrderController extends Controller
{
    public function __construct(
        private readonly CreateOrderHandler $createOrderHandler,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {}

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $command = new CreateOrderCommand(
            userId: $request->validated('user_id'),
            items: $request->validated('items'),
            currency: $request->validated('currency', 'USD'),
        );

        $orderId = $this->createOrderHandler->handle($command);

        return new JsonResponse(
            data: ['id' => $orderId, 'message' => 'Order created successfully.'],
            status: Response::HTTP_CREATED,
        );
    }

    public function show(string $id): JsonResponse
    {
        $order = $this->orderRepository->findById(Uuid::fromString($id));

        if ($order === null) {
            return new JsonResponse(
                data: ['message' => 'Order not found.'],
                status: Response::HTTP_NOT_FOUND,
            );
        }

        return new JsonResponse(data: $order);
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderRepository->all();

        return new JsonResponse(data: [
            'data' => $orders,
            'total' => $this->orderRepository->count(),
        ]);
    }

    public function byUser(string $userId): JsonResponse
    {
        $orders = $this->orderRepository->findByUserId(Uuid::fromString($userId));

        return new JsonResponse(data: ['data' => $orders]);
    }
}
