<?php

declare(strict_types=1);

namespace Bingely\User\Domain\Repository;

interface UserQueryRepositoryInterface
{
    public function existsByEmail(string $email): bool;

    public function existsByUsername(string $username): bool;
}
