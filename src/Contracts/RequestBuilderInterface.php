<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Contracts;

/**
 * Интерфейс для всех request builder классов.
 */
interface RequestBuilderInterface
{
    /**
     * Выполняет запрос и возвращает типизированный DTO.
     *
     * @return DtoInterface Результат выполнения запроса
     */
    public function get(): DtoInterface;
}
