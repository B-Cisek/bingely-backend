<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Client;

use Bingely\TvShow\Domain\Enum\Language;
use Bingely\TvShow\Infrastructure\Tmdb\Enum\TmdbEndpoint;
use Bingely\TvShow\Infrastructure\Tmdb\Exception\TmdbApiException;
use Bingely\TvShow\Infrastructure\Tmdb\Exception\TmdbException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TmdbClient implements TmdbClientInterface
{
    private const string BASE_URL = 'https://api.themoviedb.org/3';

    public function __construct(
        private HttpClientInterface $client,
        #[Autowire(param: 'tmdb.api.key')]
        private string $apiKey,
        private LoggerInterface $logger,
    ) {}

    public function get(TmdbEndpoint $endpoint, array $queryParams = [], Language $language = Language::ENGLISH): array
    {
        $url = self::BASE_URL.$endpoint->getPath($queryParams);

        $queryParams = [
            'api_key' => $this->apiKey,
            'language' => $language->value,
            ...$queryParams,
        ];

        try {
            $this->logger->info('TMDB API Request', [
                'endpoint' => $endpoint->value,
                'url' => $url,
                'params' => array_diff_key($queryParams, ['api_key' => '']),
            ]);

            $response = $this->client->request('GET', $url, [
                'query' => $queryParams,
                'headers' => [
                    'accept' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $data = $response->toArray(false);

            if ($statusCode >= 400) {
                throw new TmdbApiException(
                    $data['status_message'] ?? 'TMDB API error',
                    $statusCode,
                    $data,
                );
            }

            return $data;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('TMDB API Transport Error', [
                'endpoint' => $endpoint->value,
                'error' => $e->getMessage(),
            ]);

            throw new TmdbException(
                'Failed to connect to TMDB API: '.$e->getMessage(),
                previous: $e,
            );
        } catch (TmdbApiException $e) {
            $this->logger->error('TMDB API Error', [
                'endpoint' => $endpoint->value,
                'status_code' => $e->getStatusCode(),
                'response' => $e->getResponseData(),
            ]);

            throw $e;
        }
    }
}
