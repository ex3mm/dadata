<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\SuggestFio\FioSuggestionResponseDto;
use Ex3mm\Dadata\Enums\Gender;
use Ex3mm\Dadata\Exceptions\ValidationException;

/** Request builder для получения подсказок по ФИО */
final class SuggestFioRequest extends AbstractRequest
{
    private string $query = '';
    private int $count    = 10;
    /** @var list<string> */
    private array $parts    = [];
    private ?Gender $gender = null;

    /**
     * Устанавливает поисковый запрос.
     *
     * @param string $query Поисковый запрос (ФИО или его часть)
     */
    public function query(string $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Устанавливает количество подсказок.
     *
     * @param int $count Количество подсказок (1-20)
     */
    public function count(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Устанавливает части ФИО для подсказок.
     *
     * @param list<string> $parts Массив частей: NAME, SURNAME, PATRONYMIC
     */
    public function parts(array $parts): static
    {
        $this->parts = $parts;

        return $this;
    }

    /**
     * Устанавливает пол для фильтрации подсказок.
     *
     * @param Gender $gender Пол
     */
    public function gender(Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return CollectionResponse<FioSuggestionResponseDto>
     */
    #[\Override]
    public function get(): CollectionResponse
    {
        /** @var CollectionResponse<FioSuggestionResponseDto> */
        return parent::get();
    }

    protected function validate(): void
    {
        if ($this->query === '' || $this->query === '0') {
            throw new ValidationException(
                message: 'Поисковый запрос не может быть пустым',
                statusCode: 0,
                responseBody: '',
                errors: ['required'],
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

        if ($this->parts !== []) {
            $data['parts'] = $this->parts;
        }

        if ($this->gender !== null) {
            $data['gender'] = $this->gender->value;
        }

        return $data;
    }
}
