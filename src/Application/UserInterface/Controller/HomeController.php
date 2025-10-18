<?php

declare(strict_types=1);

namespace Bingely\Application\UserInterface\Controller;

use Bingely\Application\UserInterface\Request\HomeRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['POST'])]
    public function index(#[MapRequestPayload] HomeRequest $request): JsonResponse
    {
        return $this->json([
            'data' => [
                'name' => $request->name,
                'email' => $request->email,
            ],
        ]);
    }
}
