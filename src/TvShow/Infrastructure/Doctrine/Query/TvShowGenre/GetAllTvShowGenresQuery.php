<?php

declare (strict_types=1);

namespace Bingely\TvShow\Infrastructure\Doctrine\Query\TvShowGenre;

use Bingely\TvShow\Application\Query\GetAllTvShowGenres;
use Bingely\TvShow\Application\Query\Result\GetAllTvShowGenresResult;
use Bingely\TvShow\Application\Query\Result\TvShowGenre as TvShowGenreResult;
use Bingely\TvShow\Domain\Entity\TvShowGenre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TvShowGenre>
 */
class GetAllTvShowGenresQuery extends ServiceEntityRepository implements GetAllTvShowGenres
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TvShowGenre::class);
    }

    public function execute(): GetAllTvShowGenresResult
    {
        $result = $this->createQueryBuilder('tvg')
            ->getQuery()
            ->getResult();

        $tvShowGenresDto = array_map(fn(TvShowGenre $tvShowGenre) => new TvShowGenreResult(
            id: $tvShowGenre->getId()->toRfc4122(),
            tmdbId: $tvShowGenre->getTmdbId(),
            name: $tvShowGenre->getName(),
            translations: $tvShowGenre->getTranslations(),
        ), $result);

        return new GetAllTvShowGenresResult($tvShowGenresDto);
    }
}
