<?php

declare(strict_types=1);

namespace Bingely\Shared\Infrastructure\Symfony\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ApiException extends HttpException
{
    public function __construct(
        string $message = '',
        ?\Throwable $previous = null,
        array $headers = [],
        int $code = 0
    ) {
        parent::__construct($this->getDefaultStatusCode(), $message, $previous, $headers, $code);
    }

    abstract protected function getDefaultStatusCode(): int;
}
