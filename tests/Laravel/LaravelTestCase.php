<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Tests\Laravel;

use Ex3mm\Dadata\Laravel\DadataServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Базовый класс для Laravel-интеграционных тестов.
 */
abstract class LaravelTestCase extends OrchestraTestCase
{
    /**
     * Регистрация service providers для тестов.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            DadataServiceProvider::class,
        ];
    }

    /**
     * Определение переменных окружения для тестов.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('dadata.api_key', 'test_api_key_value');
        $app['config']->set('dadata.secret_key', 'test_secret_key_value');
    }
}
