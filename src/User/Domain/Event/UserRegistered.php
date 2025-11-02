<?php

declare(strict_types=1);

namespace Bingely\User\Domain\Event;

final readonly class UserRegistered
{
    public function __construct(public string $userId) {}
}
