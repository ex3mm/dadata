<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyResponseDto;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyStateStatus;
use Ex3mm\Dadata\Enums\PartyType;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для поиска организации по ИНН или ОГРН.
 */
final class FindPartyRequest extends AbstractRequest
{
    private string $query                = '';
    private int $count                   = 10;
    private ?string $kpp                 = null;
    private ?PartyBranchType $branchType = null;
    private ?PartyType $type             = null;
    /** @var list<PartyStateStatus> */
    private array $status = [];

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
     * Ограничение по типу подразделения: MAIN или BRANCH.
     */
    public function branchType(PartyBranchType $branchType): static
    {
        $this->branchType = $branchType;

        return $this;
    }

    /**
     * Ограничение по типу организации: LEGAL или INDIVIDUAL.
     */
    public function type(PartyType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Ограничение по статусу организации.
     *
     * @param list<PartyStateStatus> $status
     */
    public function status(array $status): static
    {
        $this->status = $status;

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

        if ($this->count > 300) {
            throw new ValidationException(
                message: 'Количество результатов не может быть больше 300',
                statusCode: 0,
                responseBody: '',
                errors: ['max_count'],
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

        if ($this->branchType !== null) {
            $data['branch_type'] = $this->branchType->value;
        }

        if ($this->type !== null) {
            $data['type'] = $this->type->value;
        }

        if ($this->status !== []) {
            $data['status'] = array_map(
                static fn (PartyStateStatus $item): string => $item->value,
                $this->status
            );
        }

        return $data;
    }
}
