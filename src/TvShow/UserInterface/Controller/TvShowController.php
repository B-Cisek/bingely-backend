<?php

declare(strict_types=1);

namespace Bingely\TvShow\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TvShowController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(param: "tmdb.api.key")] private string $apiKey,
    )
    {
    }

    #[Route('/api/tv-show/', name: 'tv-show', methods: ['GET'])]
    public function test()
    {
        $queryParameters = http_build_query([
            'api_key' => $this->apiKey,
            'sort_by' => 'popularity.desc',
            'page' => 1,
        ]);

        $url = 'https://api.themoviedb.org/3/tv/popular?'.$queryParameters;
        $urlGenre = 'https://api.themoviedb.org/3/genre/tv/list?'.$queryParameters;

        $result = $this->client->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);

        dd($result->toArray()['results'][0]);
    }
}
