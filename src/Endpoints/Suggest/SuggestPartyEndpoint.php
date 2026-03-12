<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Endpoints\Suggest;

use Ex3mm\Dadata\Contracts\DtoInterface;
use Ex3mm\Dadata\DTO\Response\SuggestParty\SuggestPartyResponse;
use Ex3mm\Dadata\Endpoints\AbstractEndpoint;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

/**
 * Endpoint для получения подсказок по организациям.
 */
final class SuggestPartyEndpoint extends AbstractEndpoint
{
    protected function getPath(): string
    {
        return '/suggestions/api/' . self::API_VERSION . '/rs/suggest/party';
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

        /** @var array<string, mixed> $data */
        return SuggestPartyResponse::fromArray($data, $body);
    }
}
