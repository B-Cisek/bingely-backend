<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Tmdb\Exception;

class TmdbApiException extends TmdbException
{
    /**
     * @param null|array<string, mixed> $responseData
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
     * @return null|array<string, mixed>
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
}
