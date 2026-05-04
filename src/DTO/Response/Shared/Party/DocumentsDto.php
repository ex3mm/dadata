<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO всех документов компании.
 */
final readonly class DocumentsDto
{
    public function __construct(
        public ?DocumentDto $ftsRegistration,
        public ?DocumentDto $ftsReport,
        public ?DocumentDto $pfRegistration,
        public ?DocumentDto $sifRegistration,
        public ?DocumentDto $smb,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ftsRegistration: self::extractDocument($data, 'fts_registration'),
            ftsReport: self::extractDocument($data, 'fts_report'),
            pfRegistration: self::extractDocument($data, 'pf_registration'),
            sifRegistration: self::extractDocument($data, 'sif_registration'),
            smb: self::extractDocument($data, 'smb'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractDocument(array $data, string $key): ?DocumentDto
    {
        if (!isset($data[$key]) || !is_array($data[$key])) {
            return null;
        }

        /** @var array<string, mixed> $documentData */
        $documentData = $data[$key];

        return DocumentDto::fromArray($documentData);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'fts_registration' => $this->ftsRegistration?->toArray(),
            'fts_report'       => $this->ftsReport?->toArray(),
            'pf_registration'  => $this->pfRegistration?->toArray(),
            'sif_registration' => $this->sifRegistration?->toArray(),
            'smb'              => $this->smb?->toArray(),
        ];
    }
}
