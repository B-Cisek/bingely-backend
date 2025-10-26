<?php

declare(strict_types=1);

namespace Bingely\User\Domain\Entity;

use Bingely\Shared\Domain\Entity\BaseEntity;
use Bingely\Shared\Infrastructure\Doctrine\Trait\CreatedAtTrait;
use Bingely\Shared\Infrastructure\Doctrine\Trait\UpdatedAtTrait;
use Bingely\User\Infrastructure\Doctrine\Repository\QueryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: QueryRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\UniqueConstraint(name: 'UNIQ_EMAIL', fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(type: Types::STRING)]
    private string $password;

    /** @param list<string> $roles */
    public function __construct(
        #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
        private string $username,
        #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
        private string $email,
        #[ORM\Column(type: Types::JSON)]
        private array $roles = []
    ) {
        parent::__construct();
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void {}
}
