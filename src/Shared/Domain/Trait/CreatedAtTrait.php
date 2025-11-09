<?php

declare(strict_types=1);

namespace Bingely\Shared\Domain\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;

trait CreatedAtTrait
{
    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
