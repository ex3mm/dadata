<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Party;

/**
 * DTO недостоверности сведений.
 */
final readonly class InvalidityDto
{
    public function __construct(
        public ?string $code,
        public ?InvalidityDecisionDto $decision,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $decision = null;
        if (isset($data['decision']) && is_array($data['decision'])) {
            /** @var array<string, mixed> $decisionData */
            $decisionData = $data['decision'];
            $decision     = InvalidityDecisionDto::fromArray($decisionData);
        }

        return new self(
            code: isset($data['code']) && is_string($data['code']) ? $data['code'] : null,
            decision: $decision,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code'     => $this->code,
            'decision' => $this->decision?->toArray(),
        ];
    }
}
