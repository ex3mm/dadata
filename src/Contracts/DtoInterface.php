<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Contracts;

/**
 * Интерфейс для всех Data Transfer Objects.
 */
interface DtoInterface
{
    /**
     * Создаёт DTO из массива данных.
     *
     * @param array<string, mixed> $data Данные для создания DTO
     * @param string $rawResponse Оригинальный JSON-ответ от API
     */
    public static function fromArray(array $data, string $rawResponse): static;

    /**
     * Преобразует DTO в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
