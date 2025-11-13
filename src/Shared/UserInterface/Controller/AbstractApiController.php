<?php

declare(strict_types=1);

namespace Bingely\Shared\UserInterface\Controller;

use Bingely\Shared\Infrastructure\Symfony\Exception\BadRequestException;
use Bingely\Shared\Infrastructure\Symfony\Exception\ForbiddenException;
use Bingely\Shared\Infrastructure\Symfony\Exception\NotFoundException;
use Bingely\Shared\Infrastructure\Symfony\Exception\UnauthorizedException;
use Bingely\Shared\Infrastructure\Symfony\Exception\ValidationException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractApiController
{
    protected SerializerInterface $serializer;

    #[Required]
    public function setSerializer(
        #[Autowire(service: 'serializer')]
        SerializerInterface $serializer
    ): void {
        $this->serializer = $serializer;
    }

    /**
     * Returns a JsonResponse with the given data.
     *
     * @param array<string, string|string[]> $headers
     * @param array<string, mixed>           $context
     */
    protected function json(
        mixed $data,
        int $status = Response::HTTP_OK,
        array $headers = [],
        array $context = []
    ): JsonResponse {
        $json = $this->serializer->serialize($data, 'json', $context);

        return new JsonResponse($json, $status, $headers, true);
    }

    /**
     * Returns a success response with data.
     *
     * @param array<string, string|string[]> $headers
     */
    protected function success(
        mixed $data = null,
        int $status = Response::HTTP_OK,
        array $headers = []
    ): JsonResponse {
        return $this->json(['data' => $data], $status, $headers);
    }

    /**
     * Returns a created response (201).
     *
     * @param array<string, string|string[]> $headers
     */
    protected function created(mixed $data = null, array $headers = []): JsonResponse
    {
        return $this->success($data, Response::HTTP_CREATED, $headers);
    }

    /**
     * Returns a no content response (204).
     *
     * @param array<string, string|string[]> $headers
     */
    protected function noContent(array $headers = []): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT, $headers);
    }

    /**
     * Throws a BadRequestException.
     */
    protected function badRequest(string $message = 'Bad Request', ?\Throwable $previous = null): never
    {
        throw new BadRequestException($message, $previous);
    }

    /**
     * Throws an UnauthorizedException.
     */
    protected function unauthorized(string $message = 'Unauthorized', ?\Throwable $previous = null): never
    {
        throw new UnauthorizedException($message, $previous);
    }

    /**
     * Throws a ForbiddenException.
     */
    protected function forbidden(string $message = 'Forbidden', ?\Throwable $previous = null): never
    {
        throw new ForbiddenException($message, $previous);
    }

    /**
     * Throws a NotFoundException.
     */
    protected function notFound(string $message = 'Not Found', ?\Throwable $previous = null): never
    {
        throw new NotFoundException($message, $previous);
    }

    /**
     * Throws a ValidationException.
     *
     * @param array<string, mixed> $errors
     */
    protected function validationError(array $errors, string $message = 'Validation Failed', ?\Throwable $previous = null): never
    {
        throw new ValidationException($message, $errors, $previous);
    }
}
