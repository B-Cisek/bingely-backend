<?php

declare(strict_types=1);

namespace Bingely\TvShow\UserInterface\Controller;

use Bingely\TvShow\Application\Provider\TvShowProviderInterface;
use Symfony\Component\Routing\Attribute\Route;

class TvShowController
{
    public function __construct(
        private TvShowProviderInterface $tvShowProvider,
    ) {}

    #[Route('/api/tv-show/', name: 'tv-show', methods: ['GET'])]
    public function test(): void
    {
        $result = $this->tvShowProvider->getGenres();

        dd($result);
    }
}
