<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\Shared\Bank\BankResponseDto;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для поиска банка по БИК, SWIFT, ИНН или регистрационному номеру.
 */
final class FindBankRequest extends AbstractRequest
{
    private string $query = '';
    private int $count    = 10;
    private ?string $kpp  = null;

    public function query(string $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function count(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Устанавливает КПП (для поиска филиалов по ИНН + КПП).
     */
    public function kpp(string $kpp): static
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * @return CollectionResponse<BankResponseDto>
     */
    #[\Override]
    public function get(): CollectionResponse
    {
        /** @var CollectionResponse<BankResponseDto> */
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

        if ($this->kpp !== null && $this->kpp !== '') {
            $data['kpp'] = $this->kpp;
        }

        return $data;
    }
}
