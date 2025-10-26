<?php

declare(strict_types=1);

namespace Bingely\User\Application\Command\Sync;

use Bingely\Shared\Application\Command\Sync\CommandHandler;

final readonly class RegisterUserHandler implements CommandHandler
{
    public function __invoke(RegisterUser $command)
    {
        dd($command);
    }
}
