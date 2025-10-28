<?php

declare(strict_types=1);

namespace Bingely\User\Application\Query;

interface UserExistsByUsername
{
    public function execute(string $username): bool;
}
