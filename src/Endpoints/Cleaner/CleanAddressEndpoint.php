<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Endpoints\Cleaner;

use Ex3mm\Dadata\Contracts\DtoInterface;
use Ex3mm\Dadata\DTO\Response\CleanAddress\CleanAddressResponseDto;
use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

/**
 * Endpoint для стандартизации адреса.
 */
final class CleanAddressEndpoint extends AbstractEndpoint
{
    protected function getPath(): string
    {
        return '/api/v1/clean/address';
    }

    protected function getBaseUrl(): string
    {
        return $this->config->baseUrlCleaner;
    }

    /**
     * @return array<string, string>
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Token ' . $this->config->apiKey,
            'X-Secret'      => $this->config->secretKey,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    protected function parseResponse(ResponseInterface $response): DtoInterface
    {
        $body = (string) $response->getBody();
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new ValidationException(
                message: 'Invalid JSON response: expected array/object, got ' . gettype($data),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_json_structure'],
            );
        }

        // Поддерживаем оба варианта: список объектов (API) и одиночный объект (локальная фикстура).
        $records = array_is_list($data) ? $data : [$data];

        $items = [];
        foreach ($records as $item) {
            if (is_array($item)) {
                /** @var array<string, mixed> $item */
                $items[] = CleanAddressResponseDto::fromArray($item);
            }
        }

        return new CollectionResponse(
            items: $items,
            raw: $body,
            total: count($items)
        );
    }
}
