<?php

declare(strict_types=1);

namespace Bingely\User\Application\Query;

use Bingely\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserExistsByUsernameQuery
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function execute(string $username): bool
    {
        $result = $this->entityManager->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $result > 0;
    }
}
