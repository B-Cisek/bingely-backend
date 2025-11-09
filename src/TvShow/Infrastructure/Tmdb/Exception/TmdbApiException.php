<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Exception;

class TmdbApiException extends TmdbException
{
    /**
     * @param array<string, mixed>|null $responseData
     */
    public function __construct(
        string $message,
        private readonly int $statusCode,
        private readonly ?array $responseData = null,
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
}
