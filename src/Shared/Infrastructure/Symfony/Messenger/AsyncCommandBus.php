<?php

declare(strict_types=1);

namespace Bingely\Shared\Infrastructure\Symfony\Messenger;

use Bingely\Shared\Application\Command\Async\Command;
use Bingely\Shared\Application\Command\Async\CommandBus;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class AsyncCommandBus implements CommandBus
{
    public function __construct(private MessageBusInterface $commandAsyncBus) {}

    public function dispatch(Command $command): void
    {
        try {
            $this->commandAsyncBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious() ?? $exception;
        }
    }
}
