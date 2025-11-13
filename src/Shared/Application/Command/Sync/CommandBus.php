<?php

declare(strict_types=1);

namespace Bingely\Shared\Application\Command\Sync;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
