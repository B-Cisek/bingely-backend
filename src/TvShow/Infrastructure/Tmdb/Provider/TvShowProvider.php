<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Provider;

use Bingely\TvShow\Infrastructure\Tmdb\Client\TmdbClientInterface;
use Bingely\TvShow\Infrastructure\Tmdb\Dto\TvShowCollectionDto;
use Bingely\TvShow\Infrastructure\Tmdb\Enum\Language;
use Bingely\TvShow\Infrastructure\Tmdb\Enum\TmdbEndpoint;
use Bingely\TvShow\Infrastructure\Tmdb\Filter\FilterInterface;
use Bingely\TvShow\Infrastructure\Tmdb\Genre\GenreCollectionDto;
use Bingely\TvShow\Infrastructure\Tmdb\Genre\GenreDto;
use Bingely\TvShow\Infrastructure\Tmdb\Transformer\TvShowTransformer;

class TvShowProvider implements TvShowProviderInterface
{
    public function __construct(
        private TmdbClientInterface $client,
        private TvShowTransformer $transformer,
    ) {
    }


    /**
     * Get popular TV shows
     *
     * @param Language $language
     * @param int $page
     * @param array<FilterInterface> $filters
     * @return TvShowCollectionDto
     */
    public function getPopular(
        Language $language = Language::ENGLISH,
        int $page = 1,
        array $filters = [],
    ): TvShowCollectionDto {
        $data = $this->client->get(
            TmdbEndpoint::TV_POPULAR,
            ['page' => $page],
            $language,
        );

        $collection = $this->transformer->transformCollection($data);

        return $this->applyFilters($collection, $filters);
    }

    /**
     * Apply filters to TV show collection
     *
     * @param TvShowCollectionDto $collection
     * @param array<FilterInterface> $filters
     * @return TvShowCollectionDto
     */
    private function applyFilters(TvShowCollectionDto $collection, array $filters): TvShowCollectionDto
    {
        if (empty($filters)) {
            return $collection;
        }

        $results = $collection->results;

        foreach ($filters as $filter) {
            $results = $filter->apply($results);
        }

        return new TvShowCollectionDto(
            page: $collection->page,
            results: $results,
            totalPages: $collection->totalPages,
            totalResults: count($results),
        );
    }

    public function getGenres(Language $language = Language::ENGLISH): GenreCollectionDto
    {
        $data = $this->client->get(
            endpoint: TmdbEndpoint::TV_GENRE,
            language: $language,
        );

        return new GenreCollectionDto(
            array_map(fn (array $item) => new GenreDto(
                tmdbId: $item['id'],
                name: $item['name'],
            ), $data['genres']),
        );
    }
}
