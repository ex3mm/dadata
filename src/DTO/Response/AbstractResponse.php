<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response;

use Ex3mm\Dadata\Contracts\DtoInterface;

/**
 * Базовый класс для всех DTO ответов от DaData API.
 */
abstract readonly class AbstractResponse implements DtoInterface
{
    /**
     * @param string $rawResponse Оригинальный JSON-ответ от API
     */
    public function __construct(
        public string $rawResponse,
    ) {
    }
}
