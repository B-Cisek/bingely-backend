<?php

declare(strict_types=1);

namespace Bingely\User\Application\Command\Sync;

use Bingely\Shared\Application\Command\Sync\Command;

final readonly class RegisterUser implements Command
{
    public function __construct(
        public string $email,
        public string $username,
        public string $password,
    ) {}
}
