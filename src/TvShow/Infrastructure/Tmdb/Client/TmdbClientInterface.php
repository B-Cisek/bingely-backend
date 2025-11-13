<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Client;

use Bingely\TvShow\Domain\Enum\Language;
use Bingely\TvShow\Infrastructure\Tmdb\Enum\TmdbEndpoint;

interface TmdbClientInterface
{
    /**
     * @param array<string, int|string> $queryParams
     *
     * @return array<string, mixed>
     */
    public function get(
        TmdbEndpoint $endpoint,
        array $queryParams = [],
        Language $language = Language::ENGLISH,
    ): array;
}
