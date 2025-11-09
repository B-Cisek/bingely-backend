<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Provider;

use Bingely\TvShow\Application\Dto\Genre\GenreCollectionDto;
use Bingely\TvShow\Domain\Enum\Language;
use Bingely\TvShow\Infrastructure\Tmdb\Dto\TvShowCollectionDto;

interface TvShowProviderInterface
{
    public function getPopular(
        Language $language = Language::ENGLISH,
        int $page = 1,
        array $filters = [],
    ): TvShowCollectionDto;

    public function getGenres(Language $language = Language::ENGLISH): GenreCollectionDto;
}
