<?php

declare(strict_types=1);

namespace Bingely\Shared\Infrastructure\Symfony\EventListener;

use Bingely\Shared\Domain\Exception\ConflictDomainException;
use Bingely\Shared\Infrastructure\Symfony\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final readonly class ApiExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
        private string $environment
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->logger->error($exception->getMessage(), [
            'exception' => $exception,
            'trace' => $exception->getTraceAsString(),
        ]);

        $statusCode = $this->getStatusCode($exception);

        $responseData = [
            'error' => [
                'message' => $this->getErrorMessage($exception, $statusCode),
                'code' => $statusCode,
            ],
        ];

        if ($exception instanceof UnprocessableEntityHttpException) {
            $violations = $this->extractValidationErrors($exception);
            if (!empty($violations)) {
                $responseData['error']['violations'] = $violations;
                $responseData['error']['message'] = 'Validation failed';
            }
        }

        if ($exception instanceof ValidationException && !empty($exception->getViolations())) {
            $responseData['error']['violations'] = $exception->getViolations();
        }

        if ($this->environment === 'dev') {
            $responseData['error']['debug'] = [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        $response = new JsonResponse($responseData, $statusCode);
        $event->setResponse($response);
    }

    private function getStatusCode(\Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof ConflictDomainException) {
            return Response::HTTP_CONFLICT;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function getErrorMessage(\Throwable $exception, int $statusCode): string
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getMessage() ?: $this->getDefaultMessage($statusCode);
        }

        if ($this->environment !== 'dev') {
            return $this->getDefaultMessage($statusCode);
        }

        return $exception->getMessage();
    }

    private function getDefaultMessage(int $statusCode): string
    {
        return match ($statusCode) {
            Response::HTTP_BAD_REQUEST => 'Bad Request',
            Response::HTTP_UNAUTHORIZED => 'Unauthorized',
            Response::HTTP_FORBIDDEN => 'Forbidden',
            Response::HTTP_NOT_FOUND => 'Not Found',
            Response::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            Response::HTTP_TOO_MANY_REQUESTS => 'Too Many Requests',
            default => 'Internal Server Error',
        };
    }

    /**
     * @return array<string, string[]>
     */
    private function extractValidationErrors(UnprocessableEntityHttpException $exception): array
    {
        $previous = $exception->getPrevious();

        if (!$previous || !method_exists($previous, 'getViolations')) {
            return [];
        }

        $violationList = $previous->getViolations();

        if (!$violationList instanceof ConstraintViolationListInterface) {
            return [];
        }

        $violations = [];
        foreach ($violationList as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $violations[$propertyPath][] = $violation->getMessage();
        }

        return $violations;
    }
}
