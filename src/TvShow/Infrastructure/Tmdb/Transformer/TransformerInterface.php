<?php

namespace Bingely\TvShow\Infrastructure\Tmdb\Transformer;

interface TransformerInterface
{
    /**
     * Transform API response data to DTO
     *
     * @param array<string, mixed> $data
     * @return object
     */
    public function transform(array $data): object;

    /**
     * Transform collection of API response data to DTOs
     *
     * @param array<string, mixed> $data
     * @return object
     */
    public function transformCollection(array $data): object;
}
