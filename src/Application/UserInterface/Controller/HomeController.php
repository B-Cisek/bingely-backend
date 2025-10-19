<?php

declare(strict_types=1);

namespace Bingely\Application\UserInterface\Controller;

use Bingely\Application\UserInterface\Request\HomeRequest;
use Bingely\Shared\UserInterface\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

readonly class HomeController extends AbstractApiController
{
    #[Route('/', name: 'home', methods: ['POST'])]
    public function index(#[MapRequestPayload] HomeRequest $request): JsonResponse
    {
        return $this->success([
            'name' => $request->name,
            'email' => $request->email,
        ]);
    }
}
