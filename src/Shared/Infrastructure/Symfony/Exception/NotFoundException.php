<?php

declare(strict_types=1);

namespace Bingely\Shared\Infrastructure\Symfony\Exception;

use Symfony\Component\HttpFoundation\Response;

final class NotFoundException extends ApiException
{
    protected function getDefaultStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
