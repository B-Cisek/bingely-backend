<?php

declare(strict_types=1);

namespace Bingely\User\Application\Query;

interface UserExistsByEmail
{
    public function execute(string $email): bool;
}
