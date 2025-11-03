<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Genre;

final readonly class GenreCollectionDto
{
    /** @param array<int, GenreDto> $genres */
    public function __construct(
        public array $genres,
    )
    {
    }
}
