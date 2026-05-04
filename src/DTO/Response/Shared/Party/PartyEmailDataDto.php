<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/** DTO детальных данных email компании */
final readonly class PartyEmailDataDto
{
    public function __construct(
        public ?string $local,
        public ?string $domain,
        public ?string $type,
        public ?string $source,
        public ?string $qc,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            local: isset($data['local'])   && is_string($data['local']) ? $data['local'] : null,
            domain: isset($data['domain']) && is_string($data['domain']) ? $data['domain'] : null,
            type: isset($data['type'])     && is_string($data['type']) ? $data['type'] : null,
            source: isset($data['source']) && is_string($data['source']) ? $data['source'] : null,
            qc: isset($data['qc'])         && is_string($data['qc']) ? $data['qc'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'local'  => $this->local,
            'domain' => $this->domain,
            'type'   => $this->type,
            'source' => $this->source,
            'qc'     => $this->qc,
        ];
    }
}
