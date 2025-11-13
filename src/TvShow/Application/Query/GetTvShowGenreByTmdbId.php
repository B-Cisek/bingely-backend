<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Query;

use Bingely\TvShow\Domain\Entity\TvShowGenre;

interface GetTvShowGenreByTmdbId
{
    public function execute(int $tmdbId): ?TvShowGenre;
}
