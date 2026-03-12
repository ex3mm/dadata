<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Endpoints\Cleaner;

use Ex3mm\Dadata\Contracts\DtoInterface;
use Ex3mm\Dadata\DTO\Response\CleanAddress\CleanAddressResponse;
use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

/**
 * Endpoint для стандартизации адресов (Clean Address API).
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
     * Возвращает заголовки для Cleaner API (требует secret_key).
     *
     * @return array<string, string>
     */
    #[\Override]
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

        // Clean API возвращает массив результатов
        $firstResult = is_array($data) && isset($data[0]) ? $data[0] : [];

        if (!is_array($firstResult)) {
            throw new ValidationException(
                message: 'Invalid JSON response: expected array result, got ' . gettype($firstResult),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_json_structure'],
            );
        }

        /** @var array<string, mixed> $firstResult */
        return CleanAddressResponse::fromArray($firstResult, $body);
    }
}
