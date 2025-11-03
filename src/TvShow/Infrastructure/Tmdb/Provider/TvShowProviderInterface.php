<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Provider;

use Bingely\TvShow\Infrastructure\Tmdb\Dto\TvShowCollectionDto;
use Bingely\TvShow\Infrastructure\Tmdb\Enum\Language;
use Bingely\TvShow\Infrastructure\Tmdb\Genre\GenreCollectionDto;

interface TvShowProviderInterface
{
    public function getPopular(
        Language $language = Language::ENGLISH,
        int $page = 1,
        array $filters = [],
    ): TvShowCollectionDto;

    public function getGenres(Language $language = Language::ENGLISH): GenreCollectionDto;
}
