<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared\Bank;

use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;

/**
 * DTO управления ЦБ РФ, к которому относится банк.
 */
final readonly class CbrDto
{
    public function __construct(
        public ?BankOpfDto $opf,
        public ?BankNameDto $name,
        public ?string $bic,
        public ?string $swift,
        public ?array $swifts,
        public ?string $inn,
        public ?string $kpp,
        public ?string $okpo,
        public ?string $correspondentAccount,
        public ?string $treasuryAccounts,
        public ?string $registrationNumber,
        public ?string $paymentCity,
        public ?BankStateDto $state,
        /** @var mixed Расчётно-кассовый центр. Не заполняется согласно документации API DaData. */
        public mixed $rkc,
        /** @var mixed Вложенные данные ЦБ РФ. Не заполняется согласно документации API DaData. */
        public mixed $cbr,
        public ?AddressValueDto $address,
        /** @var mixed Телефоны. Структура не описана в документации API DaData. */
        public mixed $phones,
        /** @var mixed Дополнительные коды. Структура не описана в документации API DaData. */
        public mixed $additionalCodes,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $opf = null;
        if (isset($data['opf']) && is_array($data['opf'])) {
            /** @var array<string, mixed> $opfData */
            $opfData = $data['opf'];
            $opf     = BankOpfDto::fromArray($opfData);
        }

        $name = null;
        if (isset($data['name']) && is_array($data['name'])) {
            /** @var array<string, mixed> $nameData */
            $nameData = $data['name'];
            $name     = BankNameDto::fromArray($nameData);
        }

        $state = null;
        if (isset($data['state']) && is_array($data['state'])) {
            /** @var array<string, mixed> $stateData */
            $stateData = $data['state'];
            $state     = BankStateDto::fromArray($stateData);
        }

        $address = null;
        if (isset($data['address']) && is_array($data['address'])) {
            /** @var array<string, mixed> $addressData */
            $addressData = $data['address'];
            $address     = AddressValueDto::fromArray($addressData);
        }

        $swifts = null;
        if (isset($data['swifts']) && is_array($data['swifts'])) {
            $swifts = array_filter($data['swifts'], fn ($item) => is_string($item));
            $swifts = $swifts !== [] ? array_values($swifts) : null;
        }

        return new self(
            opf: $opf,
            name: $name,
            bic: isset($data['bic'])     && is_string($data['bic']) ? $data['bic'] : null,
            swift: isset($data['swift']) && is_string($data['swift']) ? $data['swift'] : null,
            swifts: $swifts,
            inn: isset($data['inn'])                                    && is_string($data['inn']) ? $data['inn'] : null,
            kpp: isset($data['kpp'])                                    && is_string($data['kpp']) ? $data['kpp'] : null,
            okpo: isset($data['okpo'])                                  && is_string($data['okpo']) ? $data['okpo'] : null,
            correspondentAccount: isset($data['correspondent_account']) && is_string($data['correspondent_account']) ? $data['correspondent_account'] : null,
            treasuryAccounts: isset($data['treasury_accounts'])         && is_string($data['treasury_accounts']) ? $data['treasury_accounts'] : null,
            registrationNumber: isset($data['registration_number'])     && is_string($data['registration_number']) ? $data['registration_number'] : null,
            paymentCity: isset($data['payment_city'])                   && is_string($data['payment_city']) ? $data['payment_city'] : null,
            state: $state,
            rkc: $data['rkc'] ?? null,
            cbr: $data['cbr'] ?? null,
            address: $address,
            phones: $data['phones']                    ?? null,
            additionalCodes: $data['additional_codes'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'opf'                   => $this->opf?->toArray(),
            'name'                  => $this->name?->toArray(),
            'bic'                   => $this->bic,
            'swift'                 => $this->swift,
            'swifts'                => $this->swifts,
            'inn'                   => $this->inn,
            'kpp'                   => $this->kpp,
            'okpo'                  => $this->okpo,
            'correspondent_account' => $this->correspondentAccount,
            'treasury_accounts'     => $this->treasuryAccounts,
            'registration_number'   => $this->registrationNumber,
            'payment_city'          => $this->paymentCity,
            'state'                 => $this->state?->toArray(),
            'rkc'                   => $this->rkc,
            'cbr'                   => $this->cbr,
            'address'               => $this->address?->toArray(),
            'phones'                => $this->phones,
            'additional_codes'      => $this->additionalCodes,
        ];
    }
}
