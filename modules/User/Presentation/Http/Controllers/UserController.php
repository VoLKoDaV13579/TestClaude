<?php

declare(strict_types=1);

namespace Modules\User\Presentation\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Modules\User\Application\Commands\RegisterUserCommand;
use Modules\User\Application\Commands\RegisterUserHandler;
use Modules\User\Domain\Repositories\UserRepositoryInterface;
use Modules\User\Presentation\Http\Requests\RegisterUserRequest;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends Controller
{
    public function __construct(
        private readonly RegisterUserHandler $registerUserHandler,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $command = new RegisterUserCommand(
            name: $request->validated('name'),
            email: $request->validated('email'),
        );

        $userId = $this->registerUserHandler->handle($command);

        return new JsonResponse(
            data: ['id' => $userId, 'message' => 'User registered successfully.'],
            status: Response::HTTP_CREATED,
        );
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->userRepository->findById(Uuid::fromString($id));

        if ($user === null) {
            return new JsonResponse(
                data: ['message' => 'User not found.'],
                status: Response::HTTP_NOT_FOUND,
            );
        }

        return new JsonResponse(data: $user);
    }

    public function index(): JsonResponse
    {
        $users = $this->userRepository->all();

        return new JsonResponse(data: [
            'data' => $users,
            'total' => $this->userRepository->count(),
        ]);
    }
}
