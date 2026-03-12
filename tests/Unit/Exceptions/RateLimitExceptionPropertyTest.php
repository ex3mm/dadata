<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Exceptions;

use Ex3mm\Dadata\Exceptions\RateLimitException;
use Ex3mm\Dadata\Tests\TestCase;

/**
 * Property-based тесты для RateLimitException.
 *
 * Feature: package-refactoring-audit-fixes
 * Property 7: RateLimitException содержит retryAfter в секундах
 *
 * **Validates: Requirements 8.5, 15.4**
 */
final class RateLimitExceptionPropertyTest extends TestCase
{
    private const int PROPERTY_TEST_ITERATIONS = 100;

    /**
     * Property 7: RateLimitException содержит retryAfter в секундах.
     *
     * Для любого значения retryAfter, переданного в конструктор:
     * 1. Исключение должно корректно сохранять значение
     * 2. getRetryAfter() должен возвращать то же значение
     * 3. retryAfter должен быть положительным целым числом (>= 0)
     * 4. Значение должно быть выражено в секундах
     *
     * **Validates: Requirements 8.5, 15.4**
     */
    public function test_rate_limit_exception_contains_retry_after_in_seconds_property(): void
    {
        for ($iteration = 0; $iteration < self::PROPERTY_TEST_ITERATIONS; $iteration++) {
            // Генерируем случайное положительное значение retryAfter (0-3600 секунд)
            $retryAfter = mt_rand(0, 3600);

            // Создаем исключение
            $exception = new RateLimitException(
                message: 'Превышен лимит запросов',
                retryAfter: $retryAfter
            );

            // Property 1: getRetryAfter() возвращает корректное значение
            $this->assertSame(
                $retryAfter,
                $exception->getRetryAfter(),
                "Iteration {$iteration}: getRetryAfter() должен вернуть {$retryAfter}"
            );

            // Property 2: retryAfter должен быть целым числом
            $this->assertIsInt(
                $exception->getRetryAfter(),
                "Iteration {$iteration}: retryAfter должен быть int"
            );

            // Property 3: retryAfter должен быть неотрицательным (>= 0)
            $this->assertGreaterThanOrEqual(
                0,
                $exception->getRetryAfter(),
                "Iteration {$iteration}: retryAfter должен быть >= 0"
            );

            // Property 4: Сообщение исключения должно быть строкой
            $this->assertIsString(
                $exception->getMessage(),
                "Iteration {$iteration}: Сообщение исключения должно быть строкой"
            );
        }
    }

    /**
     * Property 7 (edge case): retryAfter = 0 (немедленная повторная попытка).
     */
    public function test_retry_after_zero_is_valid(): void
    {
        $exception = new RateLimitException(
            message: 'Превышен лимит запросов',
            retryAfter: 0
        );

        $this->assertSame(0, $exception->getRetryAfter());
        $this->assertGreaterThanOrEqual(0, $exception->getRetryAfter());
    }

    /**
     * Property 7 (edge case): retryAfter = 1 (минимальная задержка).
     */
    public function test_retry_after_one_second_is_valid(): void
    {
        $exception = new RateLimitException(
            message: 'Превышен лимит запросов',
            retryAfter: 1
        );

        $this->assertSame(1, $exception->getRetryAfter());
        $this->assertGreaterThan(0, $exception->getRetryAfter());
    }

    /**
     * Property 7 (edge case): retryAfter = большое значение (1 час).
     */
    public function test_retry_after_large_value_is_valid(): void
    {
        $oneHour = 3600;

        $exception = new RateLimitException(
            message: 'Превышен лимит запросов',
            retryAfter: $oneHour
        );

        $this->assertSame($oneHour, $exception->getRetryAfter());
        $this->assertGreaterThan(0, $exception->getRetryAfter());
    }

    /**
     * Property 7 (invariant): retryAfter неизменяем после создания.
     */
    public function test_retry_after_is_immutable(): void
    {
        for ($iteration = 0; $iteration < 20; $iteration++) {
            $retryAfter = mt_rand(1, 100);

            $exception = new RateLimitException(
                message: 'Превышен лимит запросов',
                retryAfter: $retryAfter
            );

            // Многократный вызов getRetryAfter() должен возвращать одно и то же значение
            $firstCall  = $exception->getRetryAfter();
            $secondCall = $exception->getRetryAfter();
            $thirdCall  = $exception->getRetryAfter();

            $this->assertSame(
                $firstCall,
                $secondCall,
                "Iteration {$iteration}: retryAfter должен быть неизменяемым"
            );

            $this->assertSame(
                $secondCall,
                $thirdCall,
                "Iteration {$iteration}: retryAfter должен быть неизменяемым"
            );

            $this->assertSame(
                $retryAfter,
                $firstCall,
                "Iteration {$iteration}: retryAfter должен сохранять исходное значение"
            );
        }
    }

    /**
     * Property 7 (consistency): Разные экземпляры с одинаковым retryAfter независимы.
     */
    public function test_different_instances_are_independent(): void
    {
        for ($iteration = 0; $iteration < 20; $iteration++) {
            $retryAfter1 = mt_rand(1, 100);
            $retryAfter2 = mt_rand(101, 200);

            $exception1 = new RateLimitException(
                message: 'Превышен лимит 1',
                retryAfter: $retryAfter1
            );

            $exception2 = new RateLimitException(
                message: 'Превышен лимит 2',
                retryAfter: $retryAfter2
            );

            // Каждый экземпляр должен хранить свое значение
            $this->assertSame(
                $retryAfter1,
                $exception1->getRetryAfter(),
                "Iteration {$iteration}: exception1 должен хранить свое значение"
            );

            $this->assertSame(
                $retryAfter2,
                $exception2->getRetryAfter(),
                "Iteration {$iteration}: exception2 должен хранить свое значение"
            );

            $this->assertNotSame(
                $exception1->getRetryAfter(),
                $exception2->getRetryAfter(),
                "Iteration {$iteration}: Разные экземпляры должны быть независимы"
            );
        }
    }

    /**
     * Property 7 (type safety): retryAfter всегда int, никогда не float или string.
     */
    public function test_retry_after_type_is_always_int(): void
    {
        $testValues = [
            0,
            1,
            10,
            60,
            300,
            3600,
            mt_rand(1, 10000),
        ];

        foreach ($testValues as $index => $value) {
            $exception = new RateLimitException(
                message: 'Превышен лимит запросов',
                retryAfter: $value
            );

            $result = $exception->getRetryAfter();

            $this->assertIsInt(
                $result,
                "Test value {$index}: retryAfter должен быть int, получен " . gettype($result)
            );

            $this->assertIsNotFloat(
                $result,
                "Test value {$index}: retryAfter не должен быть float"
            );

            $this->assertIsNotString(
                $result,
                "Test value {$index}: retryAfter не должен быть string"
            );
        }
    }

    /**
     * Property 7 (integration): RateLimitException корректно работает с RateLimiterMiddleware.
     *
     * Проверяем, что значение retryAfter, переданное из middleware, корректно сохраняется.
     */
    public function test_integration_with_rate_limiter_middleware(): void
    {
        for ($iteration = 0; $iteration < 20; $iteration++) {
            // Симулируем значение retryAfter, которое может вернуть RateLimiterInterface::availableIn()
            $availableIn = mt_rand(0, 60);

            // Создаем исключение так, как это делает RateLimiterMiddleware
            $exception = new RateLimitException(
                message: 'Превышен лимит запросов к DaData API',
                retryAfter: $availableIn
            );

            // Проверяем, что значение корректно сохранено
            $this->assertSame(
                $availableIn,
                $exception->getRetryAfter(),
                "Iteration {$iteration}: retryAfter должен совпадать с availableIn"
            );

            // Проверяем, что значение в секундах (целое число)
            $this->assertIsInt($exception->getRetryAfter());
            $this->assertGreaterThanOrEqual(0, $exception->getRetryAfter());
        }
    }

    /**
     * Property 7 (boundary): Граничные значения retryAfter обрабатываются корректно.
     */
    public function test_boundary_values_are_handled_correctly(): void
    {
        $boundaryValues = [
            0,          // Минимальное значение
            1,          // Минимальная задержка
            60,         // 1 минута
            300,        // 5 минут
            3600,       // 1 час
            86400,      // 1 день
            PHP_INT_MAX, // Максимальное значение int
        ];

        foreach ($boundaryValues as $value) {
            $exception = new RateLimitException(
                message: 'Превышен лимит запросов',
                retryAfter: $value
            );

            $this->assertSame(
                $value,
                $exception->getRetryAfter(),
                "Граничное значение {$value} должно корректно сохраняться"
            );

            $this->assertIsInt($exception->getRetryAfter());
            $this->assertGreaterThanOrEqual(0, $exception->getRetryAfter());
        }
    }
}
