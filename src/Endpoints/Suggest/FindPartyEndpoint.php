<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Endpoints\Suggest;

use Ex3mm\Dadata\Contracts\DtoInterface;
use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyResponseDto;
use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

/**
 * Endpoint для поиска организации по ИНН или ОГРН.
 */
final class FindPartyEndpoint extends AbstractEndpoint
{
    protected function getPath(): string
    {
        return '/suggestions/api/' . self::API_VERSION . '/rs/findById/party';
    }

    protected function getBaseUrl(): string
    {
        return $this->config->baseUrlSuggestions;
    }

    protected function parseResponse(ResponseInterface $response): DtoInterface
    {
        $body = (string) $response->getBody();
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new ValidationException(
                message: 'Invalid JSON response: expected array, got ' . gettype($data),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_json_structure'],
            );
        }

        $items           = [];
        $suggestionsData = $data['suggestions'] ?? [];

        if (is_array($suggestionsData)) {
            foreach ($suggestionsData as $item) {
                if (is_array($item)) {
                    /** @var array<string, mixed> $item */
                    $items[] = PartyResponseDto::fromArray($item);
                }
            }
        }

        return new CollectionResponse(
            items: $items,
            raw: $body,
            total: count($items)
        );
    }
}
