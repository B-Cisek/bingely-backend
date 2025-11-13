<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Filter;

interface FilterInterface
{
    /**
     * Apply filter to the data.
     *
     * @param array<mixed> $items
     *
     * @return array<mixed>
     */
    public function apply(array $items): array;
}
