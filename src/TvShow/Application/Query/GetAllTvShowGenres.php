<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Query;

use Bingely\TvShow\Application\Query\Result\GetAllTvShowGenresResult;

interface GetAllTvShowGenres
{
    public function execute(): GetAllTvShowGenresResult;
}
