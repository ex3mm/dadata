<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Laravel\Facades;

use Ex3mm\Dadata\Contracts\DadataClientInterface;
use Ex3mm\Dadata\DTO\Response\CleanAddress\CleanAddressResponseDto;
use Ex3mm\Dadata\DTO\Response\CollectionResponse;
use Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto;
use Ex3mm\Dadata\DTO\Response\Shared\Bank\BankResponseDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\AffiliatedPartyResponseDto;
use Ex3mm\Dadata\DTO\Response\Shared\Party\PartyResponseDto;
use Ex3mm\Dadata\Enums\AddressBound;
use Ex3mm\Dadata\Enums\AffiliatedScope;
use Ex3mm\Dadata\Enums\Language;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyStateStatus;
use Ex3mm\Dadata\Enums\PartyType;
use Ex3mm\Dadata\Exceptions\ValidationException;
use Ex3mm\Dadata\Requests\CleanAddressRequest;
use Ex3mm\Dadata\Requests\CustomRequest;
use Ex3mm\Dadata\Requests\FindAffiliatedRequest;
use Ex3mm\Dadata\Requests\FindBankRequest;
use Ex3mm\Dadata\Requests\FindPartyRequest;
use Ex3mm\Dadata\Requests\SuggestAddressRequest;
use Ex3mm\Dadata\Requests\SuggestBankRequest;
use Ex3mm\Dadata\Requests\SuggestPartyRequest;
use Illuminate\Support\Facades\Facade;

/**
 * Facade для упрощённого доступа к DadataClient.
 *
 * @method static SuggestAddressRequest suggestAddress() Создаёт request builder для подсказок по адресам
 * @method static SuggestBankRequest suggestBank() Создаёт request builder для подсказок по банкам
 * @method static SuggestPartyRequest suggestParty() Создаёт request builder для подсказок по организациям
 * @method static FindAffiliatedRequest findAffiliated() Создаёт request builder для поиска аффилированных компаний
 * @method static FindBankRequest findBank() Создаёт request builder для поиска банка по идентификаторам
 * @method static FindPartyRequest findParty() Создаёт request builder для поиска организации по ИНН или ОГРН
 * @method static CleanAddressRequest cleanAddress() Создаёт request builder для стандартизации адресов
 * @method static CustomRequest custom() Создаёт request builder для произвольных запросов
 *
 * @see DadataClientInterface
 */
final class Dadata extends Facade
{
    /**
     * Возвращает имя компонента в контейнере.
     */
    protected static function getFacadeAccessor(): string
    {
        return DadataClientInterface::class;
    }

    /**
     * Упрощённый метод для получения подсказок по адресам.
     *
     * @param string $query Поисковый запрос
     * @param int $count Количество подсказок (по умолчанию 10)
     * @param AddressBound|null $fromBound Нижняя граница детализации
     * @param AddressBound|null $toBound Верхняя граница детализации
     * @param Language $language Язык подсказок (по умолчанию RU)
     * @param array<string, mixed> $locations Географические ограничения
     *
     * @return CollectionResponse<AddressValueDto>
     *
     * @codeCoverageIgnore Сложно тестировать из-за final классов и статических вызовов
     */
    public static function getSuggestAddress(
        string $query,
        int $count = 10,
        ?AddressBound $fromBound = null,
        ?AddressBound $toBound = null,
        Language $language = Language::RU,
        array $locations = []
    ): CollectionResponse {
        $request = static::suggestAddress()
            ->query($query)
            ->count($count)
            ->language($language);

        if ($fromBound !== null) {
            $request->fromBound($fromBound);
        }

        if ($toBound !== null) {
            $request->toBound($toBound);
        }

        if ($locations !== []) {
            $request->locations($locations);
        }

        return $request->get();
    }

    /**
     * Упрощённый метод для получения подсказок по банкам.
     *
     * @param string $query Поисковый запрос
     * @param int $count Количество подсказок (по умолчанию 10)
     * @param list<string> $status Ограничение по статусу банка
     * @param list<string> $type Ограничение по типу банка
     * @param array<string, mixed> $locations Ограничение по региону или городу
     * @param array<string, mixed> $locationsBoost Приоритет города при ранжировании
     *
     * @return CollectionResponse<BankResponseDto>
     */
    public static function getSuggestBank(
        string $query,
        int $count = 10,
        array $status = [],
        array $type = [],
        array $locations = [],
        array $locationsBoost = []
    ): CollectionResponse {
        $request = static::suggestBank()
            ->query($query)
            ->count($count);

        if ($status !== []) {
            $request->status($status);
        }

        if ($type !== []) {
            $request->type($type);
        }

        if ($locations !== []) {
            $request->locations($locations);
        }

        if ($locationsBoost !== []) {
            $request->locationsBoost($locationsBoost);
        }

        return $request->get();
    }

    /**
     * Упрощённый метод для подсказок по организациям.
     *
     * @param string $query Поисковый запрос
     * @param int $count Количество подсказок (по умолчанию 10)
     * @param PartyType|null $type Ограничение по типу организации
     * @param list<PartyStateStatus> $status Ограничение по статусу организации
     * @param list<string> $okved Ограничение по коду ОКВЭД
     * @param array<string, mixed> $locations Ограничение по региону или городу
     * @param array<string, mixed> $locationsBoost Приоритет города при ранжировании
     *
     * @return CollectionResponse<PartyResponseDto>
     */
    public static function getSuggestParty(
        string $query,
        int $count = 10,
        ?PartyType $type = null,
        array $status = [],
        array $okved = [],
        array $locations = [],
        array $locationsBoost = []
    ): CollectionResponse {
        $request = static::suggestParty()
            ->query($query)
            ->count($count);

        if ($type !== null) {
            $request->type($type);
        }

        if ($status !== []) {
            $request->status($status);
        }

        if ($okved !== []) {
            $request->okved($okved);
        }

        if ($locations !== []) {
            $request->locations($locations);
        }

        if ($locationsBoost !== []) {
            $request->locationsBoost($locationsBoost);
        }

        return $request->get();
    }

    /**
     * Упрощённый метод для поиска аффилированных компаний по ИНН.
     *
     * @param string $query ИНН учредителя или руководителя
     * @param int $count Количество результатов (по умолчанию 10)
     * @param list<AffiliatedScope> $scope Область поиска: FOUNDERS, MANAGERS
     *
     * @return CollectionResponse<AffiliatedPartyResponseDto>
     */
    public static function getFindAffiliated(
        string $query,
        int $count = 10,
        array $scope = []
    ): CollectionResponse {
        $request = static::findAffiliated()
            ->query($query)
            ->count($count);

        if ($scope !== []) {
            $request->scope($scope);
        }

        return $request->get();
    }

    /**
     * Упрощённый метод поиска банка по БИК, SWIFT, ИНН или регистрационному номеру.
     *
     * @param string $query Идентификатор банка (БИК/SWIFT/ИНН/регистрационный номер)
     * @param int $count Количество результатов (по умолчанию 10)
     * @param string|null $kpp КПП (используется вместе с ИНН для филиалов)
     *
     * @return CollectionResponse<BankResponseDto>
     */
    public static function getFindBank(
        string $query,
        int $count = 10,
        ?string $kpp = null
    ): CollectionResponse {
        $request = static::findBank()
            ->query($query)
            ->count($count);

        if ($kpp !== null && $kpp !== '') {
            $request->kpp($kpp);
        }

        return $request->get();
    }

    /**
     * Упрощённый метод поиска организации по ИНН или ОГРН.
     *
     * @param string $query ИНН или ОГРН
     * @param int $count Количество результатов (по умолчанию 10)
     * @param string|null $kpp КПП (используется вместе с ИНН для филиалов)
     * @param PartyBranchType|null $branchType Ограничение: головная организация или филиал
     * @param PartyType|null $type Ограничение по типу организации
     * @param list<PartyStateStatus> $status Ограничение по статусу организации
     *
     * @return CollectionResponse<PartyResponseDto>
     */
    public static function getFindParty(
        string $query,
        int $count = 10,
        ?string $kpp = null,
        ?PartyBranchType $branchType = null,
        ?PartyType $type = null,
        array $status = []
    ): CollectionResponse {
        $request = static::findParty()
            ->query($query)
            ->count($count);

        if ($kpp !== null && $kpp !== '') {
            $request->kpp($kpp);
        }

        if ($branchType !== null) {
            $request->branchType($branchType);
        }

        if ($type !== null) {
            $request->type($type);
        }

        if ($status !== []) {
            $request->status($status);
        }

        return $request->get();
    }

    /**
     * Упрощённый метод для стандартизации адреса.
     *
     * @return CollectionResponse<CleanAddressResponseDto>
     */
    public static function getCleanAddress(string $address): CollectionResponse
    {
        return static::cleanAddress()
            ->query($address)
            ->get();
    }

    /**
     * Упрощённый метод для произвольного запроса.
     *
     * @param string $method HTTP-метод
     * @param string $endpoint Полный URL endpoint
     * @param array<string, mixed>|array<int, mixed> $payload JSON-тело запроса
     * @param array<string, string> $headers Дополнительные заголовки
     */
    public static function getCustom(
        string $method,
        string $endpoint,
        array $payload = [],
        array $headers = []
    ): string {
        if (!str_starts_with($endpoint, 'http://') && !str_starts_with($endpoint, 'https://')) {
            throw new ValidationException('getCustom() принимает только полный URL endpoint');
        }

        $request = static::custom()
            ->method($method)
            ->url($endpoint);

        if ($payload !== []) {
            $request->json($payload);
        }

        if ($headers !== []) {
            $request->headers($headers);
        }

        return $request->get();
    }
}
