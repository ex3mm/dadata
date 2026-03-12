<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Contracts;

/**
 * Интерфейс для всех request builder классов.
 */
interface RequestBuilderInterface
{
    /**
     * Отправляет запрос и возвращает типизированный DTO.
     *
     * @return DtoInterface Результат выполнения запроса
     */
    public function send(): DtoInterface;
}
