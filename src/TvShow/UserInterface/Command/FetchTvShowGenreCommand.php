<?php

declare(strict_types=1);

namespace Bingely\TvShow\UserInterface\Command;

use Bingely\Shared\Application\Command\Sync\CommandBus;
use Bingely\TvShow\Application\Command\Sync\FetchTvShowGenresCommand;
use Bingely\TvShow\Domain\Enum\Language;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'tmdb:fetch-genres',
    description: 'Fetch TV show genres from TMDB',
)]
class FetchTvShowGenreCommand extends Command
{
    public function __construct(private readonly CommandBus $commandBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'language',
            'l',
            InputOption::VALUE_REQUIRED,
            'Language for TV show genres (en, pl)',
            'en'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $languageValue = $input->getOption('language');
        $language = Language::from($languageValue);

        $output->writeln(sprintf('Fetching TV show genres in %s...', $language->value));

        $this->commandBus->dispatch(new FetchTvShowGenresCommand($language));

        $output->writeln('Genres fetched successfully!');

        return Command::SUCCESS;
    }
}
