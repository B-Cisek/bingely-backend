<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Dto;

final readonly class TvShowGenre
{
    public function __construct(
        public string $id,
        public string $name,
        public int $tmdbiId,
        public array $translations,
    )
    {
    }
}
