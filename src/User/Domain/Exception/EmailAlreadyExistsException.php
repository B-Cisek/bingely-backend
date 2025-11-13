<?php

declare(strict_types=1);

namespace Bingely\User\Domain\Exception;

use Bingely\Shared\Domain\Exception\ConflictDomainException;

final class EmailAlreadyExistsException extends ConflictDomainException
{
    public static function withEmail(string $email): self
    {
        return new self(sprintf('Email "%s" is already registered.', $email));
    }
}
