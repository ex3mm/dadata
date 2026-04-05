<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\Shared\Party\AffiliatedPartyResponseDto;
use Ex3mm\Dadata\Enums\AffiliatedScope;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для поиска аффилированных компаний.
 */
final class FindAffiliatedRequest extends AbstractRequest
{
    private string $query = '';
    private int $count    = 10;
    /** @var list<AffiliatedScope> */
    private array $scope = [];

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
     * Ограничение области поиска: FOUNDERS, MANAGERS.
     *
     * @param list<AffiliatedScope> $scope
     */
    public function scope(array $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @return CollectionResponse<AffiliatedPartyResponseDto>
     */
    #[\Override]
    public function get(): CollectionResponse
    {
        /** @var CollectionResponse<AffiliatedPartyResponseDto> */
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

        if (mb_strlen($this->query) > 300) {
            throw new ValidationException(
                message: 'Поисковый запрос не может быть длиннее 300 символов',
                statusCode: 0,
                responseBody: '',
                errors: ['max_length'],
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

        if ($this->scope !== []) {
            $data['scope'] = array_map(
                static fn (AffiliatedScope $item): string => $item->value,
                $this->scope
            );
        }

        return $data;
    }
}
