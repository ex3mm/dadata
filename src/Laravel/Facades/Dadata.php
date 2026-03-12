<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Laravel\Facades;

use Ex3mm\Dadata\Contracts\DadataClientInterface;
use Ex3mm\Dadata\Requests\CleanAddressRequest;
use Ex3mm\Dadata\Requests\FindPartyRequest;
use Ex3mm\Dadata\Requests\RawRequest;
use Ex3mm\Dadata\Requests\SuggestAddressRequest;
use Ex3mm\Dadata\Requests\SuggestPartyRequest;
use Illuminate\Support\Facades\Facade;

/**
 * Facade для упрощённого доступа к DadataClient.
 *
 * @method static CleanAddressRequest cleanAddress() Создаёт request builder для стандартизации адресов
 * @method static SuggestAddressRequest suggestAddress() Создаёт request builder для подсказок по адресам
 * @method static SuggestPartyRequest suggestParty() Создаёт request builder для подсказок по организациям
 * @method static FindPartyRequest findParty() Создаёт request builder для поиска организаций по ИНН/ОГРН
 * @method static RawRequest raw() Создаёт request builder для произвольных запросов
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
}
