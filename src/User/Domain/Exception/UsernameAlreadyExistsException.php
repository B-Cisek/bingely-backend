<?php

declare(strict_types=1);

namespace Bingely\User\Domain\Exception;

use Bingely\Shared\Domain\Exception\ConflictDomainException;

final class UsernameAlreadyExistsException extends ConflictDomainException
{
    public static function withUsername(string $username): self
    {
        return new self(sprintf('Username "%s" is already taken.', $username));
    }
}
