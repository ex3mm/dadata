<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/** DTO телефона компании */
final readonly class PartyPhoneDto
{
    public function __construct(
        public ?string $value,
        public ?string $unrestrictedValue,
        public ?PartyPhoneDataDto $data,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $phoneData = null;
        if (isset($data['data']) && is_array($data['data'])) {
            /** @var array<string, mixed> $dataArray */
            $dataArray = $data['data'];
            $phoneData = PartyPhoneDataDto::fromArray($dataArray);
        }

        return new self(
            value: isset($data['value'])                          && is_string($data['value']) ? $data['value'] : null,
            unrestrictedValue: isset($data['unrestricted_value']) && is_string($data['unrestricted_value']) ? $data['unrestricted_value'] : null,
            data: $phoneData,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'value'              => $this->value,
            'unrestricted_value' => $this->unrestrictedValue,
            'data'               => $this->data?->toArray(),
        ];
    }
}
