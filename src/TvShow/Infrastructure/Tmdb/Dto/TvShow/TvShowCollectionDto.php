<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Dto;

final readonly class TvShowCollectionDto
{
    /**
     * @param array<TvShowDto> $results
     */
    public function __construct(
        public int $page,
        public array $results,
        public int $totalPages,
        public int $totalResults,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'results' => array_map(fn (TvShowDto $tvShow) => $tvShow->toArray(), $this->results),
            'total_pages' => $this->totalPages,
            'total_results' => $this->totalResults,
        ];
    }
}
