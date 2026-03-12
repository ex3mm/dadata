<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Exceptions;

use Ex3mm\Dadata\Exceptions\ApiException;
use Ex3mm\Dadata\Exceptions\ConfigurationException;
use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Exceptions\RateLimitException;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Tests\TestCase;
use GuzzleHttp\Psr7\Response;

final class ExceptionHierarchyTest extends TestCase
{
    public function test_all_exceptions_extend_dadata_exception(): void
    {
        $this->assertInstanceOf(DadataException::class, new ApiException('test', 500, '{}'));
        $this->assertInstanceOf(DadataException::class, new ValidationException('test', 400, '{}'));
        $this->assertInstanceOf(DadataException::class, new ConfigurationException('test'));
        $this->assertInstanceOf(DadataException::class, new RateLimitException('test', 429, '', 60));
    }

    public function test_api_exception_from_response_parses_status_correctly(): void
    {
        $response  = new Response(404, [], '{"error": "Not found"}');
        $exception = ApiException::fromResponse($response);

        $this->assertSame(404, $exception->getStatusCode());
        $this->assertStringContainsString('404', $exception->getMessage());
        $this->assertSame('{"error": "Not found"}', $exception->getRawResponse());
    }

    public function test_api_exception_handles_empty_body(): void
    {
        $response  = new Response(500, [], '');
        $exception = ApiException::fromResponse($response);

        $this->assertSame(500, $exception->getStatusCode());
        $this->assertStringContainsString('Нет описания ошибки', $exception->getMessage());
    }

    public function test_validation_exception_stores_errors(): void
    {
        $errors    = ['Поле query обязательно', 'Поле count должно быть положительным'];
        $exception = new ValidationException('Ошибка валидации', 400, '{}', $errors);

        $this->assertSame($errors, $exception->getErrors());
    }

    public function test_rate_limit_exception_stores_retry_after(): void
    {
        $exception = new RateLimitException('Превышен лимит запросов', 429, '', 120);

        $this->assertSame(120, $exception->getRetryAfter());
    }

    public function test_no_exception_contains_api_key_in_message(): void
    {
        $fakeApiKey = 'secret_api_key_12345';

        // Проверяем что исключения не принимают API ключ напрямую
        $apiException = new ApiException('Error occurred', 500, '{"status": "error"}');
        $this->assertStringNotContainsString($fakeApiKey, $apiException->getMessage());

        $validationException = new ValidationException('Validation failed', 400, '{}');
        $this->assertStringNotContainsString($fakeApiKey, $validationException->getMessage());

        $configException = new ConfigurationException('Config error');
        $this->assertStringNotContainsString($fakeApiKey, $configException->getMessage());

        $rateLimitException = new RateLimitException('Rate limit exceeded', 429, '', 60);
        $this->assertStringNotContainsString($fakeApiKey, $rateLimitException->getMessage());
    }
}
