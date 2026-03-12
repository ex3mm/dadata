<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\FindParty\FindPartyResponse;
use Ex3mm\Dadata\Enums\PartyType;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для поиска организаций по ИНН/ОГРН.
 */
final class FindPartyRequest extends AbstractRequest
{
    private string $query    = '';
    private int $count       = 10;
    private ?PartyType $type = null;

    /**
     * Устанавливает ИНН или ОГРН для поиска.
     *
     * @param string $innOrOgrn ИНН (10 или 12 цифр) или ОГРН (13 или 15 цифр)
     */
    public function query(string $innOrOgrn): static
    {
        $this->query = $innOrOgrn;

        return $this;
    }

    /**
     * Устанавливает количество результатов.
     *
     * @param int $count Количество результатов
     */
    public function count(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Фильтрует по типу организации.
     *
     * @param PartyType $type Тип организации
     */
    public function type(PartyType $type): static
    {
        $this->type = $type;

        return $this;
    }

    #[\Override]
    public function send(): FindPartyResponse
    {
        /** @var FindPartyResponse */
        return parent::send();
    }

    protected function validate(): void
    {
        if ($this->query === '' || $this->query === '0') {
            throw new ValidationException(
                message: 'ИНН или ОГРН не может быть пустым',
                statusCode: 0,
                responseBody: '',
                errors: ['required'],
            );
        }

        // Валидация формата ИНН (10 или 12 цифр) или ОГРН (13 или 15 цифр)
        $length      = strlen($this->query);
        $isValidInn  = in_array($length, [10, 12], true) && ctype_digit($this->query);
        $isValidOgrn = in_array($length, [13, 15], true) && ctype_digit($this->query);

        if (! $isValidInn && ! $isValidOgrn) {
            throw new ValidationException(
                message: 'Неверный формат ИНН/ОГРН. Ожидается: ИНН (10 или 12 цифр) или ОГРН (13 или 15 цифр)',
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_format'],
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function toArray(): array
    {
        $data = [
            'query' => $this->query,
            'count' => $this->count,
        ];

        if ($this->type !== null) {
            $data['type'] = $this->type->value;
        }

        return $data;
    }
}
