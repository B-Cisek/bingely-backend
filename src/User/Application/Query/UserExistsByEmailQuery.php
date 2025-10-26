<?php

declare(strict_types=1);

namespace Bingely\User\Application\Query;

use Bingely\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserExistsByEmailQuery
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function execute(string $email): bool
    {
        $result = $this->entityManager->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $result > 0;
    }
}
