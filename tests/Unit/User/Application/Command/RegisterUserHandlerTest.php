<?php

declare(strict_types=1);

namespace Bingely\Tests\Unit\User\Application\Command;

use Bingely\User\Application\Command\Sync\RegisterUser;
use Bingely\User\Application\Command\Sync\RegisterUserHandler;
use Bingely\User\Application\Factory\UserFactory;
use Bingely\User\Application\Query\UserExistsByEmail;
use Bingely\User\Application\Query\UserExistsByUsername;
use Bingely\User\Domain\Entity\User;
use Bingely\User\Domain\Event\UserRegistered;
use Bingely\User\Domain\Exception\EmailAlreadyExistsException;
use Bingely\User\Domain\Exception\UsernameAlreadyExistsException;
use Bingely\User\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 *
 * @coversNothing
 */
final class RegisterUserHandlerTest extends TestCase
{
    private EventDispatcherInterface $eventDispatcher;
    private UserFactory $userFactory;
    private UserExistsByUsername $userExistsByUsernameQuery;
    private UserExistsByEmail $userExistsByEmailQuery;
    private UserRepositoryInterface $repository;
    private RegisterUserHandler $handler;

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->userFactory = $this->createMock(UserFactory::class);
        $this->userExistsByUsernameQuery = $this->createMock(UserExistsByUsername::class);
        $this->userExistsByEmailQuery = $this->createMock(UserExistsByEmail::class);
        $this->repository = $this->createMock(UserRepositoryInterface::class);

        $this->handler = new RegisterUserHandler(
            $this->eventDispatcher,
            $this->userFactory,
            $this->userExistsByUsernameQuery,
            $this->userExistsByEmailQuery,
            $this->repository
        );
    }

    public function testSuccessfulUserRegistration(): void
    {
        // Arrange
        $command = new RegisterUser(
            email: 'test@example.com',
            username: 'testuser',
            password: 'password123'
        );

        $user = $this->createMock(User::class);
        $userId = Uuid::v4();
        $user->method('getId')->willReturn($userId);

        $this->userExistsByUsernameQuery
            ->expects($this->once())
            ->method('execute')
            ->with('testuser')
            ->willReturn(false)
        ;

        $this->userExistsByEmailQuery
            ->expects($this->once())
            ->method('execute')
            ->with('test@example.com')
            ->willReturn(false)
        ;

        $this->userFactory
            ->expects($this->once())
            ->method('fromCommand')
            ->with($command)
            ->willReturn($user)
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($user)
        ;

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (UserRegistered $event) use ($userId) {
                return $event->userId === $userId->toString();
            }))
        ;

        // Act
        ($this->handler)($command);

        // Assert - expectations verified by mocks
    }

    public function testThrowsExceptionWhenUsernameAlreadyExists(): void
    {
        // Arrange
        $command = new RegisterUser(
            email: 'test@example.com',
            username: 'existinguser',
            password: 'password123'
        );

        $this->userExistsByUsernameQuery
            ->expects($this->once())
            ->method('execute')
            ->with('existinguser')
            ->willReturn(true)
        ;

        $this->userExistsByEmailQuery
            ->expects($this->never())
            ->method('execute')
        ;

        $this->userFactory
            ->expects($this->never())
            ->method('fromCommand')
        ;

        $this->repository
            ->expects($this->never())
            ->method('save')
        ;

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
        ;

        // Assert
        $this->expectException(UsernameAlreadyExistsException::class);
        $this->expectExceptionMessage('Username "existinguser" is already taken.');

        // Act
        ($this->handler)($command);
    }

    public function testThrowsExceptionWhenEmailAlreadyExists(): void
    {
        // Arrange
        $command = new RegisterUser(
            email: 'existing@example.com',
            username: 'testuser',
            password: 'password123'
        );

        $this->userExistsByUsernameQuery
            ->expects($this->once())
            ->method('execute')
            ->with('testuser')
            ->willReturn(false)
        ;

        $this->userExistsByEmailQuery
            ->expects($this->once())
            ->method('execute')
            ->with('existing@example.com')
            ->willReturn(true)
        ;

        $this->userFactory
            ->expects($this->never())
            ->method('fromCommand')
        ;

        $this->repository
            ->expects($this->never())
            ->method('save')
        ;

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch')
        ;

        // Assert
        $this->expectException(EmailAlreadyExistsException::class);
        $this->expectExceptionMessage('Email "existing@example.com" is already registered.');

        // Act
        ($this->handler)($command);
    }

    public function testDispatchesUserRegisteredEvent(): void
    {
        // Arrange
        $command = new RegisterUser(
            email: 'test@example.com',
            username: 'testuser',
            password: 'password123'
        );

        $user = $this->createMock(User::class);
        $userId = Uuid::v4();
        $user->method('getId')->willReturn($userId);

        $this->userExistsByUsernameQuery
            ->method('execute')
            ->willReturn(false)
        ;

        $this->userExistsByEmailQuery
            ->method('execute')
            ->willReturn(false)
        ;

        $this->userFactory
            ->method('fromCommand')
            ->willReturn($user)
        ;

        $dispatchedEvent = null;
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($event) use (&$dispatchedEvent) {
                $dispatchedEvent = $event;

                return $event;
            })
        ;

        // Act
        ($this->handler)($command);

        // Assert
        $this->assertInstanceOf(UserRegistered::class, $dispatchedEvent);
        $this->assertSame($userId->toString(), $dispatchedEvent->userId);
    }

    public function testChecksUsernameBeforeEmail(): void
    {
        // Arrange
        $command = new RegisterUser(
            email: 'test@example.com',
            username: 'existinguser',
            password: 'password123'
        );

        // Both username and email exist, but username check should fail first
        $this->userExistsByUsernameQuery
            ->expects($this->once())
            ->method('execute')
            ->with('existinguser')
            ->willReturn(true)
        ;

        // Email check should never be called because username check fails first
        $this->userExistsByEmailQuery
            ->expects($this->never())
            ->method('execute');

        // Assert
        $this->expectException(UsernameAlreadyExistsException::class);

        // Act
        ($this->handler)($command);
    }
}
