<?php

declare(strict_types=1);

namespace Bingely\Shared\Infrastructure\Symfony\Exception;

use Symfony\Component\HttpFoundation\Response;

final class ValidationException extends ApiException
{
    /** @var array<string, string> */
    private array $violations = [];

    /**
     * @param array<string, string> $violations
     */
    public function __construct(
        string $message = 'Validation failed',
        array $violations = [],
        ?\Throwable $previous = null
    ) {
        $this->violations = $violations;
        parent::__construct($message, $previous);
    }

    /**
     * @return array<string, string>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    protected function getDefaultStatusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
