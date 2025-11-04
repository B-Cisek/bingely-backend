<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Query\Result;

final readonly class TvShowGenre
{
    /**
     * @param array<string, string> $translations
     */
    public function __construct(
        public string $id,
        public int $tmdbId,
        public string $name,
        public array $translations,
    ) {}
}
