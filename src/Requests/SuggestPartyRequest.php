<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\SuggestParty\SuggestPartyResponse;
use Ex3mm\Dadata\Enums\PartyStatus;
use Ex3mm\Dadata\Enums\PartyType;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для получения подсказок по организациям.
 */
final class SuggestPartyRequest extends AbstractRequest
{
    private string $query        = '';
    private int $count           = 10;
    private ?PartyStatus $status = null;
    private ?PartyType $type     = null;

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
     * @param int $count Количество подсказок
     */
    public function count(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Фильтрует по статусу организации.
     *
     * @param PartyStatus $status Статус организации
     */
    public function status(PartyStatus $status): static
    {
        $this->status = $status;

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
    public function send(): SuggestPartyResponse
    {
        /** @var SuggestPartyResponse */
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
            'query' => $this->query,
            'count' => $this->count,
        ];

        if ($this->status !== null) {
            $data['status'] = [$this->status->value];
        }

        if ($this->type !== null) {
            $data['type'] = $this->type->value;
        }

        return $data;
    }
}
