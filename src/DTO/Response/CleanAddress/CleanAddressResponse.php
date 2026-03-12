<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\CleanAddress;

use Ex3mm\Dadata\DTO\Response\AbstractResponse;
use Ex3mm\Dadata\Exceptions\ValidationException;

/**
 * DTO для ответа Clean Address API.
 */
final readonly class CleanAddressResponse extends AbstractResponse
{
    /**
     * @param string $source Исходный адрес
     * @param string $result Стандартизированный адрес
     * @param string|null $postalCode Почтовый индекс
     * @param string|null $regionWithType Регион с типом
     * @param string|null $cityWithType Город с типом
     * @param string|null $streetWithType Улица с типом
     * @param string|null $house Номер дома
     * @param string|null $flat Номер квартиры
     * @param string|null $fiasId ФИАС ID
     * @param string|null $fiasCode ФИАС код
     * @param string|null $fiasLevel Уровень ФИАС
     * @param float|null $geoLat Широта
     * @param float|null $geoLon Долгота
     * @param int $qc Код качества разбора (0-3)
     * @param string $rawResponse Оригинальный JSON-ответ
     */
    public function __construct(
        public string $source,
        public string $result,
        public ?string $postalCode,
        public ?string $regionWithType,
        public ?string $cityWithType,
        public ?string $streetWithType,
        public ?string $house,
        public ?string $flat,
        public ?string $fiasId,
        public ?string $fiasCode,
        public ?string $fiasLevel,
        public ?float $geoLat,
        public ?float $geoLon,
        public int $qc,
        string $rawResponse,
    ) {
        parent::__construct($rawResponse);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data, string $rawResponse): static
    {
        $source = $data['source'] ?? '';
        $result = $data['result'] ?? '';

        if (!is_string($source)) {
            throw new ValidationException(
                message: 'Invalid source field: expected string, got ' . gettype($source),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_field_type'],
            );
        }

        if (!is_string($result)) {
            throw new ValidationException(
                message: 'Invalid result field: expected string, got ' . gettype($result),
                statusCode: 0,
                responseBody: '',
                errors: ['invalid_field_type'],
            );
        }

        return new self(
            source: $source,
            result: $result,
            postalCode: isset($data['postal_code'])          && is_string($data['postal_code']) ? $data['postal_code'] : null,
            regionWithType: isset($data['region_with_type']) && is_string($data['region_with_type']) ? $data['region_with_type'] : null,
            cityWithType: isset($data['city_with_type'])     && is_string($data['city_with_type']) ? $data['city_with_type'] : null,
            streetWithType: isset($data['street_with_type']) && is_string($data['street_with_type']) ? $data['street_with_type'] : null,
            house: isset($data['house'])                     && is_string($data['house']) ? $data['house'] : null,
            flat: isset($data['flat'])                       && is_string($data['flat']) ? $data['flat'] : null,
            fiasId: isset($data['fias_id'])                  && is_string($data['fias_id']) ? $data['fias_id'] : null,
            fiasCode: isset($data['fias_code'])              && is_string($data['fias_code']) ? $data['fias_code'] : null,
            fiasLevel: isset($data['fias_level'])            && is_string($data['fias_level']) ? $data['fias_level'] : null,
            geoLat: isset($data['geo_lat'])                  && (is_float($data['geo_lat']) || is_string($data['geo_lat'])) ? (float) $data['geo_lat'] : null,
            geoLon: isset($data['geo_lon'])                  && (is_float($data['geo_lon']) || is_string($data['geo_lon'])) ? (float) $data['geo_lon'] : null,
            qc: (function () use ($data): int {
                $qcValue = $data['qc'] ?? 0;
                if (is_int($qcValue)) {
                    return $qcValue;
                }
                if (is_numeric($qcValue)) {
                    return (int) $qcValue;
                }
                return 0;
            })(),
            rawResponse: $rawResponse,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'source'           => $this->source,
            'result'           => $this->result,
            'postal_code'      => $this->postalCode,
            'region_with_type' => $this->regionWithType,
            'city_with_type'   => $this->cityWithType,
            'street_with_type' => $this->streetWithType,
            'house'            => $this->house,
            'flat'             => $this->flat,
            'fias_id'          => $this->fiasId,
            'fias_code'        => $this->fiasCode,
            'fias_level'       => $this->fiasLevel,
            'geo_lat'          => $this->geoLat,
            'geo_lon'          => $this->geoLon,
            'qc'               => $this->qc,
        ];
    }
}
