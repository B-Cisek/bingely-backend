<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Command\Sync;

use Bingely\Shared\Application\Command\Sync\CommandHandler;
use Bingely\TvShow\Application\Factory\TvShowGenreFactory;
use Bingely\TvShow\Application\Query\GetAllTvShowGenres;
use Bingely\TvShow\Domain\Repository\TvShowGenreRepository;
use Bingely\TvShow\Infrastructure\Tmdb\Enum\Language;
use Bingely\TvShow\Infrastructure\Tmdb\Genre\GenreCollectionDto;
use Bingely\TvShow\Infrastructure\Tmdb\Provider\TvShowProviderInterface;

final readonly class FetchTvShowGenresCommandHandler implements CommandHandler
{
    public function __construct(
        private TvShowProviderInterface $tvShowProvider,
        private GetAllTvShowGenres $getAllTvShowGenres,
        private TvShowGenreFactory $tvShowGenreFactory,
        private TvShowGenreRepository $tvShowGenreRepository,
    )
    {
    }

    public function __invoke(FetchTvShowGenresCommand $command): void
    {
        $existTvShowGenres = $this->getMapTvShowGenres();
        $tvShowGenres = $this->tvShowProvider->getGenres($command->language);

        if (empty($existTvShowGenres) && $command->language === Language::ENGLISH) {
            $this->handleFirstFetch($tvShowGenres);
        }

        if ($existTvShowGenres) {
            foreach ($tvShowGenres->genres as $tvShowGenre) {
                if (isset($existTvShowGenres[(string) $tvShowGenre->tmdbId])) {
                    $tvShowGenreEntity = $this->tvShowGenreRepository->getByTmdbId($tvShowGenre->tmdbId);
                    $tvShowGenreEntity->setTranslations([
                        ...$tvShowGenreEntity->getTranslations(),
                        $command->language->value => $tvShowGenre->name,
                    ]);
                    $this->tvShowGenreRepository->save($tvShowGenreEntity);
                }
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

    private function handleFirstFetch(GenreCollectionDto $genreCollection): void
    {
        $genres = [];

        foreach ($genreCollection->genres as $genre) {
            $genres[] = $this->tvShowGenreFactory->create(
                tmdbId: $genre->tmdbId,
                name: $genre->name,
                translations: [
                    Language::ENGLISH->value => $genre->name,
                ]
            );
        }

        $this->tvShowGenreRepository->saveMany($genres);
    }
}
