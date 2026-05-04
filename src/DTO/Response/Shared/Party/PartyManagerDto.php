<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/** DTO менеджера/руководителя компании */
final readonly class PartyManagerDto
{
    public function __construct(
        public ?string $inn,
        public ?FioDto $fio,
        public ?string $post,
        public ?string $type,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $fio = null;
        if (isset($data['fio']) && is_array($data['fio'])) {
            /** @var array<string, mixed> $fioArray */
            $fioArray = $data['fio'];
            $fio      = FioDto::fromArray($fioArray);
        }

        return new self(
            inn: isset($data['inn']) && is_string($data['inn']) ? $data['inn'] : null,
            fio: $fio,
            post: isset($data['post']) && is_string($data['post']) ? $data['post'] : null,
            type: isset($data['type']) && is_string($data['type']) ? $data['type'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'inn'  => $this->inn,
            'fio'  => $this->fio?->toArray(),
            'post' => $this->post,
            'type' => $this->type,
        ];
    }
}
