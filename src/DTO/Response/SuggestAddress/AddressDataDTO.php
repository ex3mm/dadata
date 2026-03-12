<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\SuggestAddress;

/**
 * Детальные данные адреса из DaData API.
 */
final readonly class AddressDataDTO
{
    /**
     * @param string|null $postalCode Почтовый индекс
     * @param string|null $country Страна
     * @param string|null $region Регион
     * @param string|null $city Город
     * @param string|null $street Улица
     * @param string|null $house Дом
     * @param string|null $flat Квартира
     * @param string|null $fiasId ФИАС ID
     * @param float|null $geoLat Широта
     * @param float|null $geoLon Долгота
     */
    public function __construct(
        public ?string $postalCode,
        public ?string $country,
        public ?string $region,
        public ?string $city,
        public ?string $street,
        public ?string $house,
        public ?string $flat,
        public ?string $fiasId,
        public ?float $geoLat,
        public ?float $geoLon,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            postalCode: isset($data['postal_code']) && is_string($data['postal_code']) ? $data['postal_code'] : null,
            country: isset($data['country'])        && is_string($data['country']) ? $data['country'] : null,
            region: isset($data['region'])          && is_string($data['region']) ? $data['region'] : null,
            city: isset($data['city'])              && is_string($data['city']) ? $data['city'] : null,
            street: isset($data['street'])          && is_string($data['street']) ? $data['street'] : null,
            house: isset($data['house'])            && is_string($data['house']) ? $data['house'] : null,
            flat: isset($data['flat'])              && is_string($data['flat']) ? $data['flat'] : null,
            fiasId: isset($data['fias_id'])         && is_string($data['fias_id']) ? $data['fias_id'] : null,
            geoLat: isset($data['geo_lat'])         && (is_float($data['geo_lat']) || is_string($data['geo_lat'])) ? (float) $data['geo_lat'] : null,
            geoLon: isset($data['geo_lon'])         && (is_float($data['geo_lon']) || is_string($data['geo_lon'])) ? (float) $data['geo_lon'] : null,
        );
    }
}
