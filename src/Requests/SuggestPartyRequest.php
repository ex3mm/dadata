<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyResponseDto;
use Ex3mm\Dadata\Enums\PartyStateStatus;
use Ex3mm\Dadata\Enums\PartyType;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для подсказок по организациям.
 */
final class SuggestPartyRequest extends AbstractRequest
{
    private string $query    = '';
    private int $count       = 10;
    private ?PartyType $type = null;
    /** @var list<PartyStateStatus> */
    private array $status = [];
    /** @var list<string> */
    private array $okved = [];
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

    public function type(PartyType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param list<PartyStateStatus> $status
     */
    public function status(array $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param list<string> $okved
     */
    public function okved(array $okved): static
    {
        $this->okved = $okved;

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
     * @return CollectionResponse<PartyResponseDto>
     */
    #[\Override]
    public function get(): CollectionResponse
    {
        /** @var CollectionResponse<PartyResponseDto> */
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

        if ($this->type !== null) {
            $data['type'] = $this->type->value;
        }

        if ($this->status !== []) {
            $data['status'] = array_map(
                static fn (PartyStateStatus $item): string => $item->value,
                $this->status
            );
        }

        if ($this->okved !== []) {
            $data['okved'] = $this->okved;
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
