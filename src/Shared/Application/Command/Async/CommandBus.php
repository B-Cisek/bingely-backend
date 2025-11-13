<?php

declare(strict_types=1);

namespace Bingely\Shared\Application\Command\Async;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
