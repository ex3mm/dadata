<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\SuggestAddress\SuggestAddressResponse;
use Ex3mm\Dadata\Enums\AddressBound;
use Ex3mm\Dadata\Enums\Language;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для получения подсказок по адресам.
 */
final class SuggestAddressRequest extends AbstractRequest
{
    private string $query            = '';
    private int $count               = 10;
    private ?AddressBound $fromBound = null;
    private ?AddressBound $toBound   = null;
    private Language $language       = Language::RU;
    /** @var array<string, mixed> */
    private array $locations = [];

    /**
     * Устанавливает поисковый запрос.
     *
     * @param string $query Поисковый запрос
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
     * Устанавливает нижнюю границу детализации.
     *
     * @param AddressBound $bound Нижняя граница
     */
    public function fromBound(AddressBound $bound): static
    {
        $this->fromBound = $bound;

        return $this;
    }

    /**
     * Устанавливает верхнюю границу детализации.
     *
     * @param AddressBound $bound Верхняя граница
     */
    public function toBound(AddressBound $bound): static
    {
        $this->toBound = $bound;

        return $this;
    }

    /**
     * Устанавливает язык подсказок.
     *
     * @param Language $language Язык
     */
    public function language(Language $language): static
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Устанавливает географические ограничения.
     *
     * @param array<string, mixed> $locations Массив ограничений
     */
    public function locations(array $locations): static
    {
        $this->locations = $locations;

        return $this;
    }

    #[\Override]
    public function send(): SuggestAddressResponse
    {
        /** @var SuggestAddressResponse */
        return parent::send();
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
            'query'    => $this->query,
            'count'    => $this->count,
            'language' => $this->language->value,
        ];

        if ($this->fromBound !== null) {
            $data['from_bound'] = ['value' => $this->fromBound->value];
        }

        if ($this->toBound !== null) {
            $data['to_bound'] = ['value' => $this->toBound->value];
        }

        if ($this->locations !== []) {
            $data['locations'] = $this->locations;
        }

        return $data;
    }
}
