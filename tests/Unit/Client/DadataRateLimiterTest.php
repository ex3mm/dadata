<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Unit\Client;

use Ex3mm\Dadata\Client\DadataRateLimiter;
use Ex3mm\Dadata\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;

/**
 * Unit-тесты для DadataRateLimiter.
 *
 * Проверяют корректность работы методов attempt(), tooManyAttempts() и availableIn()
 * в различных сценариях использования.
 *
 * **Validates: Requirements 7.2, 7.3, 7.4**
 */
final class DadataRateLimiterTest extends TestCase
{
    private const int MAX_ATTEMPTS  = 5;
    private const int DECAY_SECONDS = 1;
    private const string TEST_KEY   = 'test_key';

    /**
     * Тест: attempt() возвращает true при доступном лимите.
     *
     * Когда количество запросов меньше maxAttempts, метод attempt()
     * должен возвращать true и добавлять новый timestamp.
     *
     * **Validates: Requirements 7.2**
     */
    public function test_attempt_returns_true_when_limit_available(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        // Кеш пустой - нет предыдущих запросов
        $cache->method('get')
            ->willReturn([]);

        // Ожидаем, что будет сохранен новый timestamp
        $cache->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo('dadata_rate_limiter_' . self::TEST_KEY),
                $this->callback(function ($timestamps) {
                    return is_array($timestamps)
                        && count($timestamps) === 1
                        && is_float($timestamps[0]);
                }),
                self::DECAY_SECONDS + 1
            )
            ->willReturn(true);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->attempt(self::TEST_KEY);

        $this->assertTrue($result, 'attempt() должен вернуть true при доступном лимите');
    }

    /**
     * Тест: attempt() возвращает false при превышении лимита.
     *
     * Когда количество запросов достигло maxAttempts, метод attempt()
     * должен возвращать false и не добавлять новый timestamp.
     *
     * **Validates: Requirements 7.2**
     */
    public function test_attempt_returns_false_when_limit_exceeded(): void
    {
        $now = microtime(true);

        // Создаем массив с MAX_ATTEMPTS свежими timestamps
        $existingTimestamps = [];
        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            $existingTimestamps[] = $now - 0.1; // 0.1 секунды назад
        }

        $cache = $this->createMock(CacheInterface::class);

        // Возвращаем полный набор timestamps
        $cache->method('get')
            ->willReturn($existingTimestamps);

        // set() не должен вызываться, так как лимит превышен
        $cache->expects($this->never())
            ->method('set');

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->attempt(self::TEST_KEY);

        $this->assertFalse($result, 'attempt() должен вернуть false при превышении лимита');
    }

    /**
     * Тест: attempt() возвращает true после истечения decay period.
     *
     * Когда старые timestamps отфильтровываются, лимит снова становится доступным.
     *
     * **Validates: Requirements 7.2**
     */
    public function test_attempt_returns_true_after_decay_period(): void
    {
        $now = microtime(true);

        // Создаем массив со старыми timestamps (старше decay period)
        $oldTimestamps = [];
        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            $oldTimestamps[] = $now - self::DECAY_SECONDS - 0.5; // 1.5 секунды назад
        }

        $cache = $this->createMock(CacheInterface::class);

        // Первый вызов get() для проверки лимита
        // Второй вызов get() для добавления нового timestamp
        $cache->method('get')
            ->willReturn($oldTimestamps);

        // Ожидаем сохранение нового timestamp
        $cache->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo('dadata_rate_limiter_' . self::TEST_KEY),
                $this->callback(function ($timestamps) {
                    // Старые timestamps отфильтрованы, остался только новый
                    return is_array($timestamps)
                        && count($timestamps) === 1
                        && is_float($timestamps[0]);
                }),
                self::DECAY_SECONDS + 1
            )
            ->willReturn(true);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->attempt(self::TEST_KEY);

        $this->assertTrue($result, 'attempt() должен вернуть true после истечения decay period');
    }

    /**
     * Тест: tooManyAttempts() возвращает false при доступном лимите.
     *
     * Когда количество запросов меньше maxAttempts, метод tooManyAttempts()
     * должен возвращать false.
     *
     * **Validates: Requirements 7.3**
     */
    public function test_too_many_attempts_returns_false_when_limit_available(): void
    {
        $now = microtime(true);

        // Создаем массив с меньшим количеством timestamps, чем maxAttempts
        $timestamps = [];
        for ($i = 0; $i < self::MAX_ATTEMPTS - 2; $i++) {
            $timestamps[] = $now - 0.1;
        }

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn($timestamps);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->tooManyAttempts(self::TEST_KEY);

        $this->assertFalse($result, 'tooManyAttempts() должен вернуть false при доступном лимите');
    }

    /**
     * Тест: tooManyAttempts() возвращает true при превышении лимита.
     *
     * Когда количество запросов достигло или превысило maxAttempts,
     * метод tooManyAttempts() должен возвращать true.
     *
     * **Validates: Requirements 7.3**
     */
    public function test_too_many_attempts_returns_true_when_limit_exceeded(): void
    {
        $now = microtime(true);

        // Создаем массив с MAX_ATTEMPTS свежими timestamps
        $timestamps = [];
        for ($i = 0; $i < self::MAX_ATTEMPTS; $i++) {
            $timestamps[] = $now - 0.1;
        }

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn($timestamps);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->tooManyAttempts(self::TEST_KEY);

        $this->assertTrue($result, 'tooManyAttempts() должен вернуть true при превышении лимита');
    }

    /**
     * Тест: tooManyAttempts() возвращает false для пустого кеша.
     *
     * Когда нет предыдущих запросов, лимит не превышен.
     *
     * **Validates: Requirements 7.3**
     */
    public function test_too_many_attempts_returns_false_for_empty_cache(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn([]);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->tooManyAttempts(self::TEST_KEY);

        $this->assertFalse($result, 'tooManyAttempts() должен вернуть false для пустого кеша');
    }

    /**
     * Тест: availableIn() возвращает 0 при доступном лимите.
     *
     * Когда лимит не превышен, метод availableIn() должен возвращать 0,
     * так как запрос можно выполнить немедленно.
     *
     * **Validates: Requirements 7.4**
     */
    public function test_available_in_returns_zero_when_limit_available(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn([]);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->availableIn(self::TEST_KEY);

        $this->assertSame(0, $result, 'availableIn() должен вернуть 0 при доступном лимите');
    }

    /**
     * Тест: availableIn() возвращает корректное время ожидания.
     *
     * Когда лимит превышен, метод availableIn() должен вернуть количество секунд
     * до момента, когда самый старый timestamp выйдет за пределы decay period.
     *
     * **Validates: Requirements 7.4**
     */
    public function test_available_in_returns_correct_wait_time(): void
    {
        $now = microtime(true);

        // Создаем timestamp 0.3 секунды назад
        // Он станет доступным через (1.0 - 0.3) = 0.7 секунды
        $oldestTimestamp = $now - 0.3;

        $timestamps = [$oldestTimestamp];
        for ($i = 0; $i < self::MAX_ATTEMPTS - 1; $i++) {
            $timestamps[] = $now - 0.1;
        }

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn($timestamps);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->availableIn(self::TEST_KEY);

        // Ожидаем примерно 1 секунду (с учетом округления вверх)
        // Точное значение: ceil(1.0 - 0.3) = ceil(0.7) = 1
        $this->assertSame(1, $result, 'availableIn() должен вернуть корректное время ожидания');
        $this->assertGreaterThanOrEqual(0, $result, 'availableIn() не должен возвращать отрицательное значение');
    }

    /**
     * Тест: availableIn() возвращает 0 для пустого кеша.
     *
     * Когда нет предыдущих запросов, время ожидания равно 0.
     *
     * **Validates: Requirements 7.4**
     */
    public function test_available_in_returns_zero_for_empty_cache(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn([]);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->availableIn(self::TEST_KEY);

        $this->assertSame(0, $result, 'availableIn() должен вернуть 0 для пустого кеша');
    }

    /**
     * Тест: availableIn() никогда не возвращает отрицательное значение.
     *
     * Даже если самый старый timestamp уже вышел за пределы decay period,
     * метод должен вернуть 0, а не отрицательное число.
     *
     * **Validates: Requirements 7.4**
     */
    public function test_available_in_never_returns_negative(): void
    {
        $now = microtime(true);

        // Создаем timestamp, который уже вышел за пределы decay period
        $oldTimestamp = $now - self::DECAY_SECONDS - 1.0;

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn([$oldTimestamp]);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->availableIn(self::TEST_KEY);

        $this->assertGreaterThanOrEqual(0, $result, 'availableIn() не должен возвращать отрицательное значение');
    }

    /**
     * Тест: availableIn() корректно работает с несколькими timestamps.
     *
     * Метод должен использовать самый старый timestamp для расчета времени ожидания.
     *
     * **Validates: Requirements 7.4**
     */
    public function test_available_in_uses_oldest_timestamp(): void
    {
        $now = microtime(true);

        // Создаем несколько timestamps с разным возрастом
        $timestamps = [
            $now - 0.8, // Самый старый - через 0.2 секунды станет доступным
            $now - 0.5,
            $now - 0.3,
            $now - 0.1,
        ];

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturn($timestamps);

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: self::MAX_ATTEMPTS,
            decaySeconds: self::DECAY_SECONDS
        );

        $result = $rateLimiter->availableIn(self::TEST_KEY);

        // Ожидаем ceil(1.0 - 0.8) = ceil(0.2) = 1
        $this->assertSame(1, $result, 'availableIn() должен использовать самый старый timestamp');
    }

    /**
     * Тест: последовательные вызовы attempt() корректно увеличивают счетчик.
     *
     * Каждый успешный вызов attempt() должен добавлять новый timestamp,
     * пока не будет достигнут лимит.
     *
     * **Validates: Requirements 7.2**
     */
    public function test_sequential_attempts_increment_counter(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $timestampsInCache = [];

        // Настраиваем mock для отслеживания состояния
        $cache->method('get')
            ->willReturnCallback(function () use (&$timestampsInCache) {
                return $timestampsInCache;
            });

        $cache->method('set')
            ->willReturnCallback(function ($key, $timestamps) use (&$timestampsInCache) {
                $timestampsInCache = $timestamps;
                return true;
            });

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: 3, // Используем маленький лимит для теста
            decaySeconds: self::DECAY_SECONDS
        );

        // Первая попытка - успешна
        $result1 = $rateLimiter->attempt(self::TEST_KEY);
        $this->assertTrue($result1, 'Первая попытка должна быть успешной');

        // Вторая попытка - успешна
        $result2 = $rateLimiter->attempt(self::TEST_KEY);
        $this->assertTrue($result2, 'Вторая попытка должна быть успешной');

        // Третья попытка - успешна
        $result3 = $rateLimiter->attempt(self::TEST_KEY);
        $this->assertTrue($result3, 'Третья попытка должна быть успешной');

        // Четвертая попытка - превышен лимит
        $result4 = $rateLimiter->attempt(self::TEST_KEY);
        $this->assertFalse($result4, 'Четвертая попытка должна быть отклонена');
    }

    /**
     * Тест: разные ключи имеют независимые лимиты.
     *
     * Rate limiter должен отслеживать лимиты отдельно для каждого ключа.
     *
     * **Validates: Requirements 7.2**
     */
    public function test_different_keys_have_independent_limits(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $cacheStorage = [];

        $cache->method('get')
            ->willReturnCallback(function ($key) use (&$cacheStorage) {
                return $cacheStorage[$key] ?? [];
            });

        $cache->method('set')
            ->willReturnCallback(function ($key, $timestamps) use (&$cacheStorage) {
                $cacheStorage[$key] = $timestamps;
                return true;
            });

        $rateLimiter = new DadataRateLimiter(
            storage: $cache,
            maxAttempts: 1, // Лимит = 1 для простоты
            decaySeconds: self::DECAY_SECONDS
        );

        // Первый ключ - успешная попытка
        $result1 = $rateLimiter->attempt('key1');
        $this->assertTrue($result1, 'Первая попытка для key1 должна быть успешной');

        // Второй ключ - успешная попытка (независимый лимит)
        $result2 = $rateLimiter->attempt('key2');
        $this->assertTrue($result2, 'Первая попытка для key2 должна быть успешной');

        // Первый ключ - превышен лимит
        $result3 = $rateLimiter->attempt('key1');
        $this->assertFalse($result3, 'Вторая попытка для key1 должна быть отклонена');

        // Второй ключ - превышен лимит
        $result4 = $rateLimiter->attempt('key2');
        $this->assertFalse($result4, 'Вторая попытка для key2 должна быть отклонена');
    }
}
