<?php

declare(strict_types=1);

namespace Bingely\TvShow\Domain\Entity;

use Bingely\Shared\Domain\Entity\BaseEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;

#[ORM\Entity]
#[ORM\Table(name: '`tv_show_genre`')]
class TvShowGenre extends BaseEntity
{
    public function __construct(
        #[ORM\Column(type: Types::INTEGER, unique: true)]
        private int $tmdbId,

        #[Column(type: Types::STRING, length: 50)]
        private string $name,

        #[Column(type: Types::JSON)]
        private array $translations = []
    )
    {
        parent::__construct();
    }



    public function getTmdbId(): int
    {
        return $this->tmdbId;
    }

    public function setTmdbId(int $tmdbId): TvShowGenre
    {
        $this->tmdbId = $tmdbId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): TvShowGenre
    {
        $this->name = $name;
        return $this;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function setTranslations(array $translations): TvShowGenre
    {
        $this->translations = $translations;
        return $this;
    }
}
