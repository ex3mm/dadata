<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\CleanAddress\CleanAddressResponseDto;
use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для стандартизации адреса.
 */
final class CleanAddressRequest extends AbstractRequest
{
    private string $query = '';

    /**
     * Устанавливает адрес для стандартизации.
     */
    public function query(string $query): static
    {
        $this->query = trim($query);

        return $this;
    }

    /**
     * @return CollectionResponse<CleanAddressResponseDto>
     */
    #[\Override]
    public function get(): CollectionResponse
    {
        /** @var CollectionResponse<CleanAddressResponseDto> */
        return parent::get();
    }

    protected function validate(): void
    {
        if ($this->query === '' || $this->query === '0') {
            throw new ValidationException(
                message: 'Адрес для стандартизации не может быть пустым',
                statusCode: 0,
                responseBody: '',
                errors: ['required'],
            );
        }
    }

    /**
     * @return array<int, string>
     */
    protected function toArray(): array
    {
        // API clean/address принимает JSON-массив строк.
        return [$this->query];
    }
}
