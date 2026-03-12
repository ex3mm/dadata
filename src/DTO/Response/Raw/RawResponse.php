<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Raw;

use Ex3mm\Dadata\DTO\Response\AbstractResponse;

/**
 * DTO для произвольных ответов от DaData API.
 */
final readonly class RawResponse extends AbstractResponse
{
    /**
     * @param array<string, mixed> $data Данные ответа
     * @param string $rawResponse Оригинальный JSON-ответ
     */
    public function __construct(
        public array $data,
        string $rawResponse,
    ) {
        parent::__construct($rawResponse);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data, string $rawResponse): static
    {
        return new self($data, $rawResponse);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
