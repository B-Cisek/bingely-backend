<?php

declare(strict_types=1);

namespace Bingely\User\UserInterface\Request;

use Bingely\Shared\Application\Command\Sync\Command;
use Bingely\User\Application\Command\Sync\RegisterUser;
use Symfony\Component\Validator\Constraints as Assert;

final class RegisterUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        public string $username,
        #[Assert\NotBlank]
        public string $password,
    ) {}

    public function toCommand(): Command
    {
        return new RegisterUser(
            email: $this->email,
            username: $this->username,
            password: $this->password,
        );
    }
}
