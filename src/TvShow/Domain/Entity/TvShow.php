<?php

declare(strict_types=1);

namespace Bingely\TvShow\Domain\Entity;

use Bingely\Shared\Domain\Entity\BaseEntity;
use Bingely\Shared\Domain\Trait\CreatedAtTrait;
use Bingely\Shared\Domain\Trait\UpdatedAtTrait;
use Bingely\TvShow\Domain\Enum\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`tv_show`')]
#[ORM\HasLifecycleCallbacks]
class TvShow extends BaseEntity
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    /**
     * @var Collection<int, TvShowGenre>
     */
    #[ORM\ManyToMany(targetEntity: TvShowGenre::class)]
    #[ORM\JoinTable(name: 'tv_show_genre_mapping')]
    #[ORM\JoinColumn(name: 'tv_show_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'tv_show_genre_id', referencedColumnName: 'id')]
    private Collection $genres;

    /**
     * @var Collection<int, TvShowTranslation>
     */
    #[ORM\OneToMany(targetEntity: TvShowTranslation::class, mappedBy: 'tvShow', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    public function __construct(
        #[ORM\Column(type: Types::INTEGER, unique: true)]
        private int $tmdbId,
        #[ORM\Column(type: Types::BOOLEAN)]
        private bool $isAdult,
        #[ORM\Column(type: Types::STRING, length: 255)]
        private string $backdropPath,
        /** @var array<int, string> */
        #[ORM\Column(type: Types::JSON)]
        private array $originCountry,
        #[ORM\Column(enumType: Language::class)]
        private Language $originalLanguage,
        #[ORM\Column(type: Types::FLOAT)]
        private float $popularity,
        #[ORM\Column(type: Types::STRING, length: 255)]
        private string $posterPath,
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        private \DateTimeImmutable $firstAirDate,
        #[ORM\Column(type: Types::FLOAT)]
        private float $voteAverage,
        #[ORM\Column(type: Types::INTEGER)]
        private int $voteCount,
    ) {
        parent::__construct();
        $this->createdAt = new \DateTimeImmutable();
        $this->genres = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function getTmdbId(): int
    {
        return $this->tmdbId;
    }

    public function setTmdbId(int $tmdbId): TvShow
    {
        $this->tmdbId = $tmdbId;

        return $this;
    }

    public function isAdult(): bool
    {
        return $this->isAdult;
    }

    public function setIsAdult(bool $isAdult): TvShow
    {
        $this->isAdult = $isAdult;

        return $this;
    }

    public function getBackdropPath(): string
    {
        return $this->backdropPath;
    }

    public function setBackdropPath(string $backdropPath): TvShow
    {
        $this->backdropPath = $backdropPath;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getOriginCountry(): array
    {
        return $this->originCountry;
    }

    /**
     * @param array<int, string> $originCountry
     */
    public function setOriginCountry(array $originCountry): TvShow
    {
        $this->originCountry = $originCountry;

        return $this;
    }

    public function getOriginalLanguage(): Language
    {
        return $this->originalLanguage;
    }

    public function setOriginalLanguage(Language $originalLanguage): TvShow
    {
        $this->originalLanguage = $originalLanguage;

        return $this;
    }

    public function getPopularity(): float
    {
        return $this->popularity;
    }

    public function setPopularity(float $popularity): TvShow
    {
        $this->popularity = $popularity;

        return $this;
    }

    public function getPosterPath(): string
    {
        return $this->posterPath;
    }

    public function setPosterPath(string $posterPath): TvShow
    {
        $this->posterPath = $posterPath;

        return $this;
    }

    public function getFirstAirDate(): \DateTimeImmutable
    {
        return $this->firstAirDate;
    }

    public function setFirstAirDate(\DateTimeImmutable $firstAirDate): TvShow
    {
        $this->firstAirDate = $firstAirDate;

        return $this;
    }

    public function getVoteAverage(): float
    {
        return $this->voteAverage;
    }

    public function setVoteAverage(float $voteAverage): TvShow
    {
        $this->voteAverage = $voteAverage;

        return $this;
    }

    public function getVoteCount(): int
    {
        return $this->voteCount;
    }

    public function setVoteCount(int $voteCount): TvShow
    {
        $this->voteCount = $voteCount;

        return $this;
    }

    /**
     * @return Collection<int, TvShowGenre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(TvShowGenre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(TvShowGenre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }

    /**
     * @return Collection<int, TvShowTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(TvShowTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setTvShow($this);
        }

        return $this;
    }

    public function removeTranslation(TvShowTranslation $translation): self
    {
        $this->translations->removeElement($translation);

        return $this;
    }
}
