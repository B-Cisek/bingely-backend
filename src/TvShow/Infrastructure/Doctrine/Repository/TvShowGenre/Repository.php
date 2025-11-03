<?php

declare(strict_types=1);

namespace Bingely\TvShow\Infrastructure\Doctrine\Repository\TvShowGenre;

use Bingely\TvShow\Domain\Entity\TvShowGenre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TvShowGenre>
 */
class Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TvShowGenre::class);
    }

    public function save(TvShowGenre $tvShowGenre): void
    {
        $this->getEntityManager()->persist($tvShowGenre);
        $this->getEntityManager()->flush();
    }

    /** @param array<int, TvShowGenre> $tvShowGenres */
    public function saveMany(array $tvShowGenres): void
    {
        foreach ($tvShowGenres as $tvShowGenre) {
            $this->getEntityManager()->persist($tvShowGenre);
        }

        $this->getEntityManager()->flush();
    }
}
