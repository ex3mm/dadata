<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Requests;

use Ex3mm\Dadata\DTO\Response\CleanAddress\CleanAddressResponse;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * Request builder для стандартизации адресов.
 */
final class CleanAddressRequest extends AbstractRequest
{
    private string $address = '';

    /**
     * Устанавливает адрес для стандартизации.
     *
     * @param string $address Адрес
     */
    public function address(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Отправляет запрос и возвращает типизированный DTO.
     */
    #[\Override]
    public function send(): CleanAddressResponse
    {
        /** @var CleanAddressResponse */
        return parent::send();
    }

    protected function validate(): void
    {
        if ($this->address === '' || $this->address === '0') {
            throw new ValidationException(
                message: 'Адрес не может быть пустым',
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
        return [$this->address];
    }
}
