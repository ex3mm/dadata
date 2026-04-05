<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

final readonly class PartyManagementDto
{
    public function __construct(
        public ?string $name,
        public ?string $post,
        public ?int $startDate,
        public ?bool $disqualified,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            post: isset($data['post']) && is_string($data['post']) ? $data['post'] : null,
            startDate: self::extractInt($data, 'start_date'),
            disqualified: isset($data['disqualified']) && is_bool($data['disqualified']) ? $data['disqualified'] : null,
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
            'name'         => $this->name,
            'post'         => $this->post,
            'start_date'   => $this->startDate,
            'disqualified' => $this->disqualified,
        ];
    }
}
