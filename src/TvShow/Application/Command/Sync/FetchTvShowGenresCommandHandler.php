<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Command\Sync;

use Bingely\Shared\Application\Command\Sync\CommandHandler;
use Bingely\TvShow\Application\Query\GetAllTvShowGenres;
use Bingely\TvShow\Infrastructure\Tmdb\Provider\TvShowProviderInterface;

final readonly class FetchTvShowGenresCommandHandler implements CommandHandler
{
    public function __construct(
        private TvShowProviderInterface $tvShowProvider,
        private GetAllTvShowGenres $getAllTvShowGenres,
    )
    {
    }

    public function __invoke(FetchTvShowGenresCommand $command): void
    {
        $existTvShowGenres = $this->getMapTvShowGenres();
        $tvShowGenres = $this->tvShowProvider->getGenres();

        foreach ($tvShowGenres as $tvShowGenre) {
            if ($existTvShowGenres[(string) $tvShowGenre->tmdbId]) {
                // update or add lang
            } else {
                // create
            }
        }

    }

    private function getMapTvShowGenres(): array
    {
        $tvShowGenres = $this->getAllTvShowGenres->execute();

        $mapTvShowGenres = [];

        foreach ($tvShowGenres->genres as $tvShowGenre) {
            $mapTvShowGenres[$tvShowGenre->tmdbId] = $tvShowGenre;
        }

        return $mapTvShowGenres;
    }
}
