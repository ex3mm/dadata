<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO лицензии компании.
 */
final readonly class LicenseDto
{
    public function __construct(
        public ?string $series,
        public ?string $number,
        public ?int $issueDate,
        public ?string $issueAuthority,
        public ?int $suspendDate,
        public ?string $suspendAuthority,
        public ?int $validFrom,
        public ?int $validTo,
        /** @var list<string>|null */
        public ?array $activities,
        /** @var mixed Адреса действия лицензии. Структура вариативна, оставлено как mixed. */
        public mixed $addresses,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            series: isset($data['series']) && is_string($data['series']) ? $data['series'] : null,
            number: isset($data['number']) && is_string($data['number']) ? $data['number'] : null,
            issueDate: self::extractInt($data, 'issue_date'),
            issueAuthority: isset($data['issue_authority']) && is_string($data['issue_authority']) ? $data['issue_authority'] : null,
            suspendDate: self::extractInt($data, 'suspend_date'),
            suspendAuthority: isset($data['suspend_authority']) && is_string($data['suspend_authority']) ? $data['suspend_authority'] : null,
            validFrom: self::extractInt($data, 'valid_from'),
            validTo: self::extractInt($data, 'valid_to'),
            activities: self::extractActivities($data),
            addresses: $data['addresses'] ?? null,
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
     * @param array<string, mixed> $data
     *
     * @return list<string>|null
     */
    private static function extractActivities(array $data): ?array
    {
        if (!isset($data['activities']) || !is_array($data['activities'])) {
            return null;
        }

        $activities = [];
        foreach ($data['activities'] as $activity) {
            if (is_string($activity)) {
                $activities[] = $activity;
            }
        }

        return $activities !== [] ? $activities : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'series'            => $this->series,
            'number'            => $this->number,
            'issue_date'        => $this->issueDate,
            'issue_authority'   => $this->issueAuthority,
            'suspend_date'      => $this->suspendDate,
            'suspend_authority' => $this->suspendAuthority,
            'valid_from'        => $this->validFrom,
            'valid_to'          => $this->validTo,
            'activities'        => $this->activities,
            'addresses'         => $this->addresses,
        ];
    }
}
