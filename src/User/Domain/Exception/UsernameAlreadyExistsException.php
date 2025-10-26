<?php

declare(strict_types=1);

namespace Bingely\User\Domain\Exception;

use Bingely\Shared\Infrastructure\Symfony\Exception\ConflictException;

final class UsernameAlreadyExistsException extends ConflictException
{
    public static function withUsername(string $username): self
    {
        return new self(sprintf('Username "%s" is already taken.', $username));
    }
}
