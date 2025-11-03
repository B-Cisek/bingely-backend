<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Query\Result;

final readonly class GetAllTvShowGenresResult
{
    /**
     * @param array<int, TvShowGenre> $genres
     */
    public function __construct(
        public array $genres,
    ) {
    }
}
