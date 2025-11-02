<?php

declare(strict_types=1);

namespace Bingely\Tests\Functional\User;

use Bingely\Tests\Functional\WebTestCase;
use Bingely\User\Domain\Entity\User;
use Bingely\User\Domain\Repository\UserRepositoryInterface;

final class RegisterUserControllerTest extends WebTestCase
{
    private UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = static::getContainer()->get(UserRepositoryInterface::class);
    }

    public function testSuccessfulUserRegistration(): void
    {
        // Arrange
        $userData = [
            'email' => 'newuser@example.com',
            'username' => 'newuser',
            'password' => 'SecurePassword123!',
        ];

        // Act
        $this->jsonRequest('POST', '/api/register', $userData);


        // Assert
        $this->assertResponseStatusCodeSame(204);

        // Verify user was created in database
        $this->entityManager->clear();
        $user = $this->userRepository->findOneBy(['username' => 'newuser']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('newuser', $user->getUsername());
        $this->assertSame('newuser@example.com', $user->getEmail());
        $this->assertContains('ROLE_USER', $user->getRoles());

        $this->assertNotSame('SecurePassword123!', $user->getPassword());
        $this->assertStringStartsWith('$', $user->getPassword()); // BCrypt/Argon2 hash starts with $
    }

    public function testValidationFailsForInvalidEmail(): void
    {
        // Arrange
        $userData = [
            'email' => 'invalid-email',
            'username' => 'testuser',
            'password' => 'password123',
        ];

        // Act
        $this->jsonRequest('POST', '/api/register', $userData);

        // Assert
        $this->assertResponseStatusCodeSame(422);

        $responseData = $this->getResponseData();
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('violations', $responseData['error']);

        // Check that email validation failed
        $violations = $responseData['error']['violations'];
        $this->assertArrayHasKey('email', $violations);
    }

    public function testValidationFailsForBlankEmail(): void
    {
        // Arrange
        $userData = [
            'email' => '',
            'username' => 'testuser',
            'password' => 'password123',
        ];

        // Act
        $this->jsonRequest('POST', '/api/register', $userData);

        // Assert
        $this->assertResponseStatusCodeSame(422);

        $responseData = $this->getResponseData();
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('violations', $responseData['error']);
    }

    public function testValidationFailsForBlankUsername(): void
    {
        // Arrange
        $userData = [
            'email' => 'test@example.com',
            'username' => '',
            'password' => 'password123',
        ];

        // Act
        $this->jsonRequest('POST', '/api/register', $userData);

        // Assert
        $this->assertResponseStatusCodeSame(422);

        $responseData = $this->getResponseData();
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('violations', $responseData['error']);
    }

    public function testValidationFailsForBlankPassword(): void
    {
        // Arrange
        $userData = [
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => '',
        ];

        // Act
        $this->jsonRequest('POST', '/api/register', $userData);

        // Assert
        $this->assertResponseStatusCodeSame(422);

        $responseData = $this->getResponseData();
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('violations', $responseData['error']);
    }

    public function testRegistrationFailsWhenUsernameAlreadyExists(): void
    {
        // Arrange - Create existing user
        $existingUser = new User(
            username: 'existinguser',
            email: 'existing@example.com',
            roles: ['ROLE_USER']
        );
        $existingUser->setPassword('$2y$13$hashedpassword');
        $this->userRepository->save($existingUser);
        $this->entityManager->flush();

        $userData = [
            'email' => 'different@example.com',
            'username' => 'existinguser', // Same username
            'password' => 'password123',
        ];

        // Act
        $this->jsonRequest('POST', '/api/register', $userData);

        // Assert
        $this->assertResponseStatusCodeSame(409);

        $responseData = $this->getResponseData();
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('message', $responseData['error']);
        $this->assertStringContainsString('existinguser', $responseData['error']['message']);
        $this->assertStringContainsString('already taken', $responseData['error']['message']);
    }

    public function testRegistrationFailsWhenEmailAlreadyExists(): void
    {
        // Arrange - Create existing user
        $existingUser = new User(
            username: 'existinguser',
            email: 'existing@example.com',
            roles: ['ROLE_USER']
        );
        $existingUser->setPassword('$2y$13$hashedpassword');
        $this->userRepository->save($existingUser);
        $this->entityManager->flush();

        $userData = [
            'email' => 'existing@example.com',
            'username' => 'differentuser',
            'password' => 'password123',
        ];

        // Act
        $this->jsonRequest('POST', '/api/register', $userData);

        // Assert
        $this->assertResponseStatusCodeSame(409);

        $responseData = $this->getResponseData();
        $this->assertArrayHasKey('error', $responseData);
        $this->assertArrayHasKey('message', $responseData['error']);
        $this->assertStringContainsString('existing@example.com', $responseData['error']['message']);
        $this->assertStringContainsString('already registered', $responseData['error']['message']);
    }
}
