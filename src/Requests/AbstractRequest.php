<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\Contracts\DtoInterface;
use Ex3mm\Dadata\Contracts\RequestBuilderInterface;
use Ex3mm\Dadata\Endpoints\AbstractEndpoint;

/**
 * Базовый класс для всех request builder классов.
 */
abstract class AbstractRequest implements RequestBuilderInterface
{
    public function __construct(
        protected readonly AbstractEndpoint $endpoint,
    ) {
    }

    /**
     * Отправляет запрос и возвращает типизированный DTO.
     */
    public function send(): DtoInterface
    {
        $this->validate();

        return $this->endpoint->execute($this->toArray());
    }

    /**
     * Валидирует параметры запроса.
     *
     * @throws \Ex3mm\Dadata\Exceptions\ValidationException
     */
    abstract protected function validate(): void;

    /**
     * Преобразует параметры запроса в массив для API.
     *
     * @return array<string, mixed>|array<int, string>
     */
    abstract protected function toArray(): array;
}
