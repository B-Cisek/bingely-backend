<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Factory;

use Bingely\TvShow\Domain\Entity\TvShowGenre;

readonly class TvShowGenreFactory
{
    /** @param array<string, string> $translations */
    public function create(int $tmdbId, string $name, array $translations = []): TvShowGenre
    {
        return new TvShowGenre(
            tmdbId: $tmdbId,
            name: $name,
            translations: $translations,
        );
    }
}
