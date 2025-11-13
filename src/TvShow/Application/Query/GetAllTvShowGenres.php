<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Query;

use Bingely\TvShow\Domain\Entity\TvShowGenre;

interface GetAllTvShowGenres
{
    /** @return array<int, TvShowGenre> */
    public function execute(): array;
}
