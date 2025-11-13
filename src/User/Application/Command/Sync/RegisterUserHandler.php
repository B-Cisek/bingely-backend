<?php

declare(strict_types=1);

namespace Bingely\User\Application\Command\Sync;

use Bingely\Shared\Application\Command\Sync\CommandHandler;
use Bingely\User\Application\Factory\UserFactory;
use Bingely\User\Domain\Event\UserRegistered;
use Bingely\User\Domain\Exception\EmailAlreadyExistsException;
use Bingely\User\Domain\Exception\UsernameAlreadyExistsException;
use Bingely\User\Domain\Repository\UserQueryRepositoryInterface;
use Bingely\User\Domain\Repository\UserRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class RegisterUserHandler implements CommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private UserFactory $userFactory,
        private UserQueryRepositoryInterface $userQueryRepository,
        private UserRepositoryInterface $repository
    ) {}

    public function __invoke(RegisterUser $command): void
    {
        if ($this->userQueryRepository->existsByUsername($command->username)) {
            throw UsernameAlreadyExistsException::withUsername($command->username);
        }

        if ($this->userQueryRepository->existsByEmail($command->email)) {
            throw EmailAlreadyExistsException::withEmail($command->email);
        }

        $user = $this->userFactory->fromCommand($command);

        $this->repository->save($user);

        $this->eventDispatcher->dispatch(new UserRegistered($user->getId()->toString()));
    }
}
