<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Client;

use Ex3mm\Dadata\Client\DadataRateLimiter;
use Ex3mm\Dadata\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;

/**
 * Property-based тесты для DadataRateLimiter.
 *
 * Feature: package-refactoring-audit-fixes
 * Property 6: Sliding window rate limiter корректно фильтрует timestamps
 *
 * **Validates: Requirements 7.7, 15.2**
 */
final class DadataRateLimiterPropertyTest extends TestCase
{
    private const int PROPERTY_TEST_ITERATIONS = 100;
    private const int DECAY_SECONDS            = 1;

    /**
     * Property 6: Sliding window rate limiter корректно фильтрует timestamps.
     *
     * Для любого набора timestamps, метод getTimestamps() должен:
     * 1. Фильтровать timestamps старше decay period (1 секунда)
     * 2. Возвращать только актуальные timestamps
     * 3. Все возвращенные timestamps должны быть > (now - decaySeconds)
     *
     * **Validates: Requirements 7.7, 15.2**
     */
    public function test_sliding_window_filters_old_timestamps_property(): void
    {
        for ($iteration = 0; $iteration < self::PROPERTY_TEST_ITERATIONS; $iteration++) {
            // Генерируем случайный набор timestamps
            $now        = microtime(true);
            $timestamps = $this->generateRandomTimestamps($now);

            // Создаем mock кеша с нашими timestamps
            $cache = $this->createMockCacheWithTimestamps($timestamps);

            // Создаем rate limiter
            $rateLimiter = new DadataRateLimiter(
                storage: $cache,
                maxAttempts: 10,
                decaySeconds: self::DECAY_SECONDS
            );

            // Получаем отфильтрованные timestamps через рефлексию
            $reflection = new \ReflectionClass($rateLimiter);
            $method     = $reflection->getMethod('getTimestamps');

            $filtered = $method->invoke($rateLimiter, 'test_key');

            // Property 1: Результат должен быть массивом
            $this->assertIsArray(
                $filtered,
                "Iteration {$iteration}: getTimestamps() должен возвращать массив"
            );

            // Property 2: Все timestamps в результате должны быть float
            foreach ($filtered as $ts) {
                $this->assertIsFloat(
                    $ts,
                    "Iteration {$iteration}: Каждый timestamp должен быть float"
                );
            }

            // Property 3: Все timestamps должны быть свежими (не старше decay period)
            $cutoff = $now - self::DECAY_SECONDS;
            foreach ($filtered as $ts) {
                $this->assertGreaterThan(
                    $cutoff,
                    $ts,
                    "Iteration {$iteration}: Timestamp {$ts} должен быть > cutoff {$cutoff}"
                );
            }

            // Property 4: Количество отфильтрованных <= количества исходных
            $this->assertLessThanOrEqual(
                count($timestamps),
                count($filtered),
                "Iteration {$iteration}: Отфильтрованных timestamps не может быть больше исходных"
            );

            // Property 5: Старые timestamps должны быть отфильтрованы
            $expectedFiltered = array_filter(
                $timestamps,
                fn ($ts) => is_float($ts) && $ts > $cutoff
            );

            $this->assertCount(
                count($expectedFiltered),
                $filtered,
                "Iteration {$iteration}: Количество отфильтрованных timestamps должно совпадать с ожидаемым"
            );
        }
    }

    /**
     * Property 6 (edge case): Пустой массив timestamps возвращает пустой массив.
     */
    public function test_empty_timestamps_returns_empty_array(): void
    {
        $cache = $this->createMockCacheWithTimestamps([]);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: 10,
            decaySeconds: self::DECAY_SECONDS
        );

        $reflection = new \ReflectionClass($rateLimiter);
        $method     = $reflection->getMethod('getTimestamps');

        $filtered = $method->invoke($rateLimiter, 'test_key');

        $this->assertIsArray($filtered);
        $this->assertEmpty($filtered, 'Пустой массив timestamps должен вернуть пустой массив');
    }

    /**
     * Property 6 (edge case): Все старые timestamps фильтруются полностью.
     */
    public function test_all_old_timestamps_are_filtered_out(): void
    {
        $now = microtime(true);

        // Генерируем только старые timestamps (все старше decay period)
        $oldTimestamps = [];
        for ($i = 0; $i < 10; $i++) {
            $oldTimestamps[] = $now - self::DECAY_SECONDS - mt_rand(1, 100) / 10;
        }

        $cache = $this->createMockCacheWithTimestamps($oldTimestamps);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: 10,
            decaySeconds: self::DECAY_SECONDS
        );

        $reflection = new \ReflectionClass($rateLimiter);
        $method     = $reflection->getMethod('getTimestamps');

        $filtered = $method->invoke($rateLimiter, 'test_key');

        $this->assertIsArray($filtered);
        $this->assertEmpty($filtered, 'Все старые timestamps должны быть отфильтрованы');
    }

    /**
     * Property 6 (edge case): Все свежие timestamps сохраняются.
     */
    public function test_all_fresh_timestamps_are_preserved(): void
    {
        $now = microtime(true);

        // Генерируем только свежие timestamps (все в пределах decay period)
        $freshTimestamps = [];
        for ($i = 0; $i < 10; $i++) {
            $freshTimestamps[] = $now - mt_rand(0, 90) / 100; // 0-0.9 секунды назад
        }

        $cache = $this->createMockCacheWithTimestamps($freshTimestamps);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: 10,
            decaySeconds: self::DECAY_SECONDS
        );

        $reflection = new \ReflectionClass($rateLimiter);
        $method     = $reflection->getMethod('getTimestamps');

        $filtered = $method->invoke($rateLimiter, 'test_key');

        $this->assertIsArray($filtered);
        $this->assertCount(
            count($freshTimestamps),
            $filtered,
            'Все свежие timestamps должны быть сохранены'
        );
    }

    /**
     * Property 6 (edge case): Смешанные timestamps корректно фильтруются.
     */
    public function test_mixed_timestamps_are_correctly_filtered(): void
    {
        for ($iteration = 0; $iteration < 20; $iteration++) {
            $now = microtime(true);

            // Генерируем смешанный набор: половина старых, половина свежих
            $timestamps         = [];
            $expectedFreshCount = 0;

            for ($i = 0; $i < 20; $i++) {
                if ($i % 2 === 0) {
                    // Старый timestamp
                    $timestamps[] = $now - self::DECAY_SECONDS - mt_rand(1, 100) / 10;
                } else {
                    // Свежий timestamp
                    $timestamps[] = $now - mt_rand(0, 90) / 100;
                    $expectedFreshCount++;
                }
            }

            $cache = $this->createMockCacheWithTimestamps($timestamps);

            $rateLimiter = new DadataRateLimiter(
                storage: $cache,
                maxAttempts: 20,
                decaySeconds: self::DECAY_SECONDS
            );

            $reflection = new \ReflectionClass($rateLimiter);
            $method     = $reflection->getMethod('getTimestamps');

            $filtered = $method->invoke($rateLimiter, 'test_key');

            $this->assertCount(
                $expectedFreshCount,
                $filtered,
                "Iteration {$iteration}: Должно остаться {$expectedFreshCount} свежих timestamps"
            );
        }
    }

    /**
     * Property 6 (robustness): Некорректные данные в кеше обрабатываются безопасно.
     */
    public function test_handles_invalid_cache_data_gracefully(): void
    {
        $invalidData = [
            null,
            'string',
            123,
            ['not', 'floats'],
            [1, 2, 3], // integers вместо floats
            [microtime(true), 'invalid', microtime(true)], // смешанные типы
        ];

        foreach ($invalidData as $index => $data) {
            $cache = $this->createMock(CacheInterface::class);
            $cache->method('get')->willReturn($data);

            $rateLimiter = new DadataRateLimiter(
                storage: $cache,
                maxAttempts: 10,
                decaySeconds: self::DECAY_SECONDS
            );

            $reflection = new \ReflectionClass($rateLimiter);
            $method     = $reflection->getMethod('getTimestamps');

            $filtered = $method->invoke($rateLimiter, 'test_key');

            $this->assertIsArray(
                $filtered,
                "Invalid data case {$index}: Должен вернуть массив даже при некорректных данных"
            );

            // Проверяем, что все элементы результата - float
            foreach ($filtered as $ts) {
                $this->assertIsFloat(
                    $ts,
                    "Invalid data case {$index}: Все элементы результата должны быть float"
                );
            }
        }
    }

    /**
     * Генерирует случайный набор timestamps для property-теста.
     *
     * @param float $now Текущее время
     *
     * @return list<float> Массив timestamps
     */
    private function generateRandomTimestamps(float $now): array
    {
        $count      = mt_rand(0, 50); // 0-50 timestamps
        $timestamps = [];

        for ($i = 0; $i < $count; $i++) {
            // Генерируем timestamp в диапазоне от 5 секунд назад до текущего момента
            $offset       = mt_rand(0, 5000) / 1000; // 0-5 секунд
            $timestamps[] = $now - $offset;
        }

        return $timestamps;
    }

    /**
     * Создает mock кеша с заданными timestamps.
     *
     * @param list<float> $timestamps
     *
     * @return CacheInterface
     */
    private function createMockCacheWithTimestamps(array $timestamps): CacheInterface
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')
            ->willReturn($timestamps);

        return $cache;
    }
}
