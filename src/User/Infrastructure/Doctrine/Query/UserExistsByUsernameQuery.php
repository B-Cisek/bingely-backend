<?php

declare(strict_types=1);

namespace Bingely\User\Infrastructure\Doctrine\Query;

use Bingely\User\Application\Query\UserExistsByUsername;
use Bingely\User\Domain\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserExistsByUsernameQuery extends ServiceEntityRepository implements UserExistsByUsername
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function execute(string $username): bool
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
