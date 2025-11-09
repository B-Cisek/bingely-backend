<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Command\Sync;

use Bingely\Shared\Application\Command\Sync\CommandHandler;
use Bingely\TvShow\Application\Dto\Genre\GenreCollectionDto;
use Bingely\TvShow\Application\Factory\TvShowGenreFactory;
use Bingely\TvShow\Application\Provider\TvShowProviderInterface;
use Bingely\TvShow\Domain\Entity\TvShowGenre;
use Bingely\TvShow\Domain\Enum\Language;
use Bingely\TvShow\Domain\Repository\TvShowGenreRepository;

final readonly class FetchTvShowGenresCommandHandler implements CommandHandler
{
    public function __construct(
        private TvShowProviderInterface $tvShowProvider,
        private TvShowGenreRepository $repository,
        private TvShowGenreFactory $tvShowGenreFactory,
        private TvShowGenreRepository $tvShowGenreRepository,
    ) {}

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

                    if ($tvShowGenreEntity !== null) {
                        $tvShowGenreEntity->addTranslation(
                            language: $command->language,
                            name: $tvShowGenre->name
                        );
                        $this->tvShowGenreRepository->save($tvShowGenreEntity);
                    }
                }
            }
        }
    }

    /** @return array<int, TvShowGenre> */
    private function getMapTvShowGenres(): array
    {
        $tvShowGenres = $this->repository->getAll();

        $mapTvShowGenres = [];

        foreach ($tvShowGenres as $tvShowGenre) {
            $mapTvShowGenres[$tvShowGenre->getTmdbId()] = $tvShowGenre;
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
