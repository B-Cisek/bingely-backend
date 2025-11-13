<?php

declare(strict_types=1);

namespace Bingely\TvShow\Domain\Repository;

use Bingely\TvShow\Domain\Entity\TvShowGenre;

interface TvShowGenreRepository
{
    public function save(TvShowGenre $tvShowGenre): void;

    /** @param array<int, TvShowGenre> $tvShowGenres */
    public function saveMany(array $tvShowGenres): void;

    public function get(string $id): ?TvShowGenre;

    public function getByTmdbId(int $tmdbId): ?TvShowGenre;

    /** @return array<int, TvShowGenre> */
    public function getAll(): array;
}
