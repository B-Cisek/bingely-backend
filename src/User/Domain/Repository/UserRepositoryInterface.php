<?php

declare(strict_types=1);

namespace Bingely\User\Domain\Repository;

use Bingely\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;
}
