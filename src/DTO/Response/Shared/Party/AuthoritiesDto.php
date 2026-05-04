<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO всех органов власти компании.
 */
final readonly class AuthoritiesDto
{
    public function __construct(
        public ?AuthorityDto $ftsRegistration,
        public ?AuthorityDto $ftsReport,
        public ?AuthorityDto $pf,
        public ?AuthorityDto $sif,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ftsRegistration: self::extractAuthority($data, 'fts_registration'),
            ftsReport: self::extractAuthority($data, 'fts_report'),
            pf: self::extractAuthority($data, 'pf'),
            sif: self::extractAuthority($data, 'sif'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractAuthority(array $data, string $key): ?AuthorityDto
    {
        if (!isset($data[$key]) || !is_array($data[$key])) {
            return null;
        }

        /** @var array<string, mixed> $authorityData */
        $authorityData = $data[$key];

        return AuthorityDto::fromArray($authorityData);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'fts_registration' => $this->ftsRegistration?->toArray(),
            'fts_report'       => $this->ftsReport?->toArray(),
            'pf'               => $this->pf?->toArray(),
            'sif'              => $this->sif?->toArray(),
        ];
    }
}
