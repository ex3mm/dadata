<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Client\Middleware;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Middleware для повторных попыток с exponential backoff.
 */
final class RetryMiddleware
{
    /**
     * @param int $maxAttempts Максимальное количество попыток
     * @param int $retryDelay Начальная задержка в миллисекундах
     * @param LoggerInterface $logger Логгер для записи повторных попыток
     */
    public function __construct(
        private readonly int $maxAttempts,
        private readonly int $retryDelay,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Возвращает Guzzle middleware функцию.
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            /** @var array<string, mixed> $options */
            return $this->executeWithRetry($handler, $request, $options, 1);
        };
    }

    /**
     * Выполняет запрос с повторными попытками.
     *
     * @param array<string, mixed> $options
     */
    private function executeWithRetry(
        callable $handler,
        RequestInterface $request,
        array $options,
        int $attempt
    ): PromiseInterface {
        try {
            /** @var PromiseInterface $promise */
            $promise = $handler($request, $options);

            return $promise->then(
                function (ResponseInterface $response) use ($handler, $request, $options, $attempt): ResponseInterface|PromiseInterface {
                    if ($this->shouldRetry($response) && $attempt < $this->maxAttempts) {
                        $this->logger->warning(
                            "Повторная попытка {$attempt}/{$this->maxAttempts} для {$request->getUri()}",
                            ['status' => $response->getStatusCode()]
                        );

                        $this->sleep($attempt);

                        /** @var array<string, mixed> $options */
                        return $this->executeWithRetry($handler, $request, $options, $attempt + 1);
                    }

                    return $response;
                },
                function (\Throwable $exception) use ($handler, $request, $options, $attempt): PromiseInterface {
                    if ($this->shouldRetryException($exception) && $attempt < $this->maxAttempts) {
                        $this->logger->warning(
                            "Повторная попытка {$attempt}/{$this->maxAttempts} после ошибки для {$request->getUri()}",
                            ['exception' => $exception::class]
                        );

                        $this->sleep($attempt);

                        /** @var array<string, mixed> $options */
                        return $this->executeWithRetry($handler, $request, $options, $attempt + 1);
                    }

                    throw $exception;
                }
            );
        } catch (ConnectException|RequestException $exception) {
            if ($this->shouldRetryException($exception) && $attempt < $this->maxAttempts) {
                $this->logger->warning(
                    "Повторная попытка {$attempt}/{$this->maxAttempts} после исключения для {$request->getUri()}",
                    ['exception' => $exception::class]
                );

                $this->sleep($attempt);

                /** @var array<string, mixed> $options */
                return $this->executeWithRetry($handler, $request, $options, $attempt + 1);
            }

            throw $exception;
        }
    }

    /**
     * Определяет, нужно ли повторить запрос при данном ответе.
     */
    private function shouldRetry(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();

        return in_array($statusCode, [429, 500, 502, 503, 504], true);
    }

    /**
     * Определяет, нужно ли повторить запрос при данном исключении.
     */
    private function shouldRetryException(\Throwable $exception): bool
    {
        if ($exception instanceof ConnectException) {
            return true;
        }

        if ($exception instanceof RequestException) {
            $response = $exception->getResponse();

            if (!$response instanceof \Psr\Http\Message\ResponseInterface) {
                return true;
            }

            return $this->shouldRetry($response);
        }

        return false;
    }

    /**
     * Ожидает перед следующей попыткой (exponential backoff).
     */
    private function sleep(int $attempt): void
    {
        $delay = $this->retryDelay * (2 ** ($attempt - 1));
        usleep($delay * 1000);
    }
}
