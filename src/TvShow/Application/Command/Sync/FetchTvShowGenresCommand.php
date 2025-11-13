<?php

declare(strict_types=1);

namespace Bingely\TvShow\Application\Command\Sync;

use Bingely\Shared\Application\Command\Sync\Command;
use Bingely\TvShow\Domain\Enum\Language;

final readonly class FetchTvShowGenresCommand implements Command
{
    public function __construct(public Language $language = Language::ENGLISH) {}
}
