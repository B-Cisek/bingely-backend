<?php

declare(strict_types=1);

namespace Bingely\User\Application\Factory;

use Bingely\User\Application\Command\Sync\RegisterUser;
use Bingely\User\Domain\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserFactory
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function fromCommand(RegisterUser $command): User
    {
        $user = new User(
            username: $command->username,
            email: $command->email,
        );

        $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));

        return $user;
    }
}
