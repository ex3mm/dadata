<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Contracts;

/**
 * Интерфейс для всех endpoint-классов.
 */
interface EndpointInterface
{
    /**
     * Выполняет запрос к API endpoint.
     *
     * @param array<string, mixed> $body Тело запроса
     *
     * @return DtoInterface Результат выполнения запроса
     */
    public function execute(array $body): DtoInterface;
}
