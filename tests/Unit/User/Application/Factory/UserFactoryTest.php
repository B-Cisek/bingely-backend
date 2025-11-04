<?php

declare(strict_types=1);

namespace Bingely\Tests\Unit\User\Application\Factory;

use Bingely\User\Application\Command\Sync\RegisterUser;
use Bingely\User\Application\Factory\UserFactory;
use Bingely\User\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class UserFactoryTest extends TestCase
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserFactory $factory;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->factory = new UserFactory($this->passwordHasher);
    }

    public function testFromCommandCreatesUserWithCorrectData(): void
    {
        // Arrange
        $command = new RegisterUser(
            email: 'test@example.com',
            username: 'testuser',
            password: 'plain-password'
        );

        $hashedPassword = 'hashed-password';
        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with(
                $this->callback(fn (User $user) => $user->getUsername() === 'testuser' && $user->getEmail() === 'test@example.com'),
                'plain-password'
            )
            ->willReturn($hashedPassword)
        ;

        // Act
        $user = $this->factory->fromCommand($command);

        // Assert
        $this->assertSame('testuser', $user->getUsername());
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame($hashedPassword, $user->getPassword());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testFromCommandHashesPassword(): void
    {
        // Arrange
        $command = new RegisterUser(
            email: 'test@example.com',
            username: 'testuser',
            password: 'plain-password'
        );

        $hashedPassword = '$2y$13$hashedpassword';
        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->willReturn($hashedPassword)
        ;

        // Act
        $user = $this->factory->fromCommand($command);

        // Assert
        $this->assertSame($hashedPassword, $user->getPassword());
        $this->assertNotSame('plain-password', $user->getPassword());
    }

    public function testFromCommandSetsDefaultRoles(): void
    {
        // Arrange
        $command = new RegisterUser(
            email: 'test@example.com',
            username: 'testuser',
            password: 'plain-password'
        );

        $this->passwordHasher
            ->method('hashPassword')
            ->willReturn('hashed-password')
        ;

        // Act
        $user = $this->factory->fromCommand($command);

        // Assert
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}
