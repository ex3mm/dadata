<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\Shared\Bank\BankResponseDto;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для получения подсказок по банкам.
 */
final class SuggestBankRequest extends AbstractRequest
{
    private string $query = '';
    private int $count    = 10;
    /** @var list<string> */
    private array $status = [];
    /** @var list<string> */
    private array $type = [];
    /** @var array<string, mixed> */
    private array $locations = [];
    /** @var array<string, mixed> */
    private array $locationsBoost = [];

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
     * @param list<string> $status
     */
    public function status(array $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param list<string> $type
     */
    public function type(array $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param array<string, mixed> $locations
     */
    public function locations(array $locations): static
    {
        $this->locations = $locations;

        return $this;
    }

    /**
     * @param array<string, mixed> $locationsBoost
     */
    public function locationsBoost(array $locationsBoost): static
    {
        $this->locationsBoost = $locationsBoost;

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

        if ($this->status !== []) {
            $data['status'] = $this->status;
        }

        if ($this->type !== []) {
            $data['type'] = $this->type;
        }

        if ($this->locations !== []) {
            $data['locations'] = $this->locations;
        }

        if ($this->locationsBoost !== []) {
            $data['locations_boost'] = $this->locationsBoost;
        }

        return $data;
    }
}
