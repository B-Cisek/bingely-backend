<?php

declare(strict_types=1);

namespace Bingely\TvShow\Domain\Entity;

use Bingely\Shared\Domain\Entity\BaseEntity;
use Bingely\TvShow\Domain\Enum\Language;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;

#[ORM\Entity]
#[ORM\Table(name: '`tv_show_translation`')]
class TvShowTranslation extends BaseEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: TvShow::class, inversedBy: 'translations')]
        #[ORM\JoinColumn(nullable: false)]
        private TvShow $tvShow,
        #[Column(enumType: Language::class)]
        private Language $language,
        #[Column(type: Types::STRING)]
        private string $originalName,
        #[Column(type: Types::STRING)]
        private string $name,
        #[Column(type: Types::TEXT)]
        private string $overview
    ) {
        parent::__construct();
    }

    public function getTvShow(): TvShow
    {
        return $this->tvShow;
    }

    public function setTvShow(TvShow $tvShow): TvShowTranslation
    {
        $this->tvShow = $tvShow;

        return $this;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): TvShowTranslation
    {
        $this->language = $language;

        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): TvShowTranslation
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): TvShowTranslation
    {
        $this->name = $name;

        return $this;
    }

    public function getOverview(): string
    {
        return $this->overview;
    }

    public function setOverview(string $overview): TvShowTranslation
    {
        $this->overview = $overview;

        return $this;
    }
}
