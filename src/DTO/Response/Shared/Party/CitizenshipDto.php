<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/** DTO гражданства для ИП */
final readonly class CitizenshipDto
{
    public function __construct(
        public ?CitizenshipCodeDto $code,
        public ?CitizenshipNameDto $name,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $code = null;
        if (isset($data['code']) && is_array($data['code'])) {
            /** @var array<string, mixed> $codeArray */
            $codeArray = $data['code'];
            $code      = CitizenshipCodeDto::fromArray($codeArray);
        }

        $name = null;
        if (isset($data['name']) && is_array($data['name'])) {
            /** @var array<string, mixed> $nameArray */
            $nameArray = $data['name'];
            $name      = CitizenshipNameDto::fromArray($nameArray);
        }

        return new self(
            code: $code,
            name: $name,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code?->toArray(),
            'name' => $this->name?->toArray(),
        ];
    }
}
