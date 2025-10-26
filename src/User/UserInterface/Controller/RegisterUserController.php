<?php

declare(strict_types=1);

namespace Bingely\User\UserInterface\Controller;

use Bingely\Shared\Application\Command\Sync\CommandBus as SyncCommandBus;
use Bingely\Shared\UserInterface\Controller\AbstractApiController;
use Bingely\Shared\UserInterface\Request\RegisterUserRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class RegisterUserController extends AbstractApiController
{
    public function __construct(
        private SyncCommandBus $syncCommandBus,
    ) {}

    #[Route('/api/register', name: 'register', methods: ['POST'])]
    public function register(#[MapRequestPayload] RegisterUserRequest $request): Response
    {
        try {
            $this->syncCommandBus->dispatch($request->toCommand());
        } catch (\Exception) {
        }

        return $this->noContent();
    }
}
