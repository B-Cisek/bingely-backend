<?php

namespace Bingely\TvShow\Infrastructure\Tmdb\Genre;

final readonly class GenreDto
{
    public function __construct(
        public int $tmdbId,
        public string $name,
    )
    {
    }

}
