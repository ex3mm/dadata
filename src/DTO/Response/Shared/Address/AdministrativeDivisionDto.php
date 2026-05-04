<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Address;

/**
 * DTO административных делений адреса.
 */
final readonly class AdministrativeDivisionDto
{
    public function __construct(
        public ?DivisionDto $area,
        public ?DivisionDto $city,
        public ?DivisionDto $cityDistrict,
        public ?DivisionDto $settlement,
        public ?DivisionDto $planningStructure,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            area: self::extractDivision($data, 'area'),
            city: self::extractDivision($data, 'city'),
            cityDistrict: self::extractDivision($data, 'city_district'),
            settlement: self::extractDivision($data, 'settlement'),
            planningStructure: self::extractDivision($data, 'planning_structure'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractDivision(array $data, string $key): ?DivisionDto
    {
        if (!isset($data[$key]) || !is_array($data[$key])) {
            return null;
        }

        /** @var array<string, mixed> $divisionData */
        $divisionData = $data[$key];

        return DivisionDto::fromArray($divisionData);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'area'               => $this->area?->toArray(),
            'city'               => $this->city?->toArray(),
            'city_district'      => $this->cityDistrict?->toArray(),
            'settlement'         => $this->settlement?->toArray(),
            'planning_structure' => $this->planningStructure?->toArray(),
        ];
    }
}
