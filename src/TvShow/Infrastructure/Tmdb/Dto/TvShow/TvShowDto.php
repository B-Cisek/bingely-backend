<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Dto;

final readonly class TvShowDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $originalName,
        public string $overview,
        public string $posterPath,
        public string $backdropPath,
        public string $firstAirDate,
        public float $voteAverage,
        public int $voteCount,
        public float $popularity,
        public array $genreIds,
        public array $originCountry,
        public string $originalLanguage,
    ) {
    }

    public function getPosterUrl(?string $size = 'w500'): ?string
    {
        return $this->posterPath
            ? "https://image.tmdb.org/t/p/{$size}{$this->posterPath}"
            : null;
    }

    public function getBackdropUrl(?string $size = 'w1280'): ?string
    {
        return $this->backdropPath
            ? "https://image.tmdb.org/t/p/{$size}{$this->backdropPath}"
            : null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'original_name' => $this->originalName,
            'overview' => $this->overview,
            'poster_path' => $this->posterPath,
            'poster_url' => $this->getPosterUrl(),
            'backdrop_path' => $this->backdropPath,
            'backdrop_url' => $this->getBackdropUrl(),
            'first_air_date' => $this->firstAirDate,
            'vote_average' => $this->voteAverage,
            'vote_count' => $this->voteCount,
            'popularity' => $this->popularity,
            'genre_ids' => $this->genreIds,
            'origin_country' => $this->originCountry,
            'original_language' => $this->originalLanguage,
        ];
    }
}
