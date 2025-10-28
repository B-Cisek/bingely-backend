<?php

declare(strict_types=1);

namespace Bingely\User\Infrastructure\Doctrine\Query;

use Bingely\User\Application\Query\UserExistsByEmail;
use Bingely\User\Domain\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserExistsByEmailQuery extends ServiceEntityRepository implements UserExistsByEmail
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function execute(string $email): bool
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
}
