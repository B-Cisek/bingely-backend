<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Transformer;

use Bingely\TvShow\Infrastructure\Tmdb\Dto\TvShowCollectionDto;
use Bingely\TvShow\Infrastructure\Tmdb\Dto\TvShowDto;

final class TvShowTransformer implements TransformerInterface
{
    public function transform(array $data): TvShowDto
    {
        return new TvShowDto(
            id: $data['id'],
            name: $data['name'] ?? '',
            originalName: $data['original_name'] ?? '',
            overview: $data['overview'] ?? null,
            posterPath: $data['poster_path'] ?? null,
            backdropPath: $data['backdrop_path'] ?? null,
            firstAirDate: $data['first_air_date'] ?? null,
            voteAverage: (float) ($data['vote_average'] ?? 0),
            voteCount: (int) ($data['vote_count'] ?? 0),
            popularity: (float) ($data['popularity'] ?? 0),
            genreIds: $data['genre_ids'] ?? [],
            originCountry: $data['origin_country'] ?? [],
            originalLanguage: $data['original_language'] ?? '',
        );
    }

    public function transformCollection(array $data): TvShowCollectionDto
    {
        $results = array_map(
            fn(array $item) => $this->transform($item),
            $data['results'] ?? []
        );

        return new TvShowCollectionDto(
            page: $data['page'] ?? 1,
            results: $results,
            totalPages: $data['total_pages'] ?? 0,
            totalResults: $data['total_results'] ?? 0,
        );
    }
}
