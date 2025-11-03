<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Enum;

enum TmdbEndpoint: string
{
    case TV_POPULAR = '/tv/popular';
    case TV_GENRE = '/genre/tv/list';

    public function getPath(array $params = []): string
    {
        $path = $this->value;

        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', (string) $value, $path);
        }

        return $path;
    }
}
