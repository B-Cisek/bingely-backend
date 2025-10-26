<?php

declare(strict_types=1);

namespace Bingely\User\Domain\Exception;

use Bingely\Shared\Infrastructure\Symfony\Exception\ConflictException;

final class EmailAlreadyExistsException extends ConflictException
{
    public static function withEmail(string $email): self
    {
        return new self(sprintf('Email "%s" is already registered.', $email));
    }
}
