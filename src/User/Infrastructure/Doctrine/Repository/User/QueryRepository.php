<?php

declare(strict_types=1);

namespace Bingely\User\Infrastructure\Doctrine\Repository\User;

use Bingely\User\Domain\Entity\User;
use Bingely\User\Domain\Repository\UserQueryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class QueryRepository extends ServiceEntityRepository implements UserQueryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function existsByEmail(string $email): bool
    {
        $result = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $result > 0;
    }

    public function existsByUsername(string $username): bool
    {
        $result = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $result > 0;
    }
}
