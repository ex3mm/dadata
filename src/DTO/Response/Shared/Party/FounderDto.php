<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO учредителя компании.
 */
final readonly class FounderDto
{
    public function __construct(
        public ?string $ogrn,
        public ?string $inn,
        public ?string $name,
        public ?FioDto $fio,
        public ?string $hid,
        public ?string $type,
        public ?FounderShareDto $share,
        public ?InvalidityDto $invalidity,
        public ?int $startDate,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $fio = null;
        if (isset($data['fio']) && is_array($data['fio'])) {
            /** @var array<string, mixed> $fioData */
            $fioData = $data['fio'];
            $fio     = FioDto::fromArray($fioData);
        }

        $share = null;
        if (isset($data['share']) && is_array($data['share'])) {
            /** @var array<string, mixed> $shareData */
            $shareData = $data['share'];
            $share     = FounderShareDto::fromArray($shareData);
        }

        $invalidity = null;
        if (isset($data['invalidity']) && is_array($data['invalidity'])) {
            /** @var array<string, mixed> $invalidityData */
            $invalidityData = $data['invalidity'];
            $invalidity     = InvalidityDto::fromArray($invalidityData);
        }

        return new self(
            ogrn: isset($data['ogrn']) && is_string($data['ogrn']) ? $data['ogrn'] : null,
            inn: isset($data['inn'])   && is_string($data['inn']) ? $data['inn'] : null,
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            fio: $fio,
            hid: isset($data['hid'])   && is_string($data['hid']) ? $data['hid'] : null,
            type: isset($data['type']) && is_string($data['type']) ? $data['type'] : null,
            share: $share,
            invalidity: $invalidity,
            startDate: self::extractInt($data, 'start_date'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractInt(array $data, string $key): ?int
    {
        if (!isset($data[$key])) {
            return null;
        }

        return is_int($data[$key]) ? $data[$key] : (is_numeric($data[$key]) ? (int) $data[$key] : null);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'ogrn'       => $this->ogrn,
            'inn'        => $this->inn,
            'name'       => $this->name,
            'fio'        => $this->fio?->toArray(),
            'hid'        => $this->hid,
            'type'       => $this->type,
            'share'      => $this->share?->toArray(),
            'invalidity' => $this->invalidity?->toArray(),
            'start_date' => $this->startDate,
        ];
    }
}
