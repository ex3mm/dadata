<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Contracts;

use Ex3mm\Dadata\Requests\CleanAddressRequest;
use Ex3mm\Dadata\Requests\FindPartyRequest;
use Ex3mm\Dadata\Requests\RawRequest;
use Ex3mm\Dadata\Requests\SuggestAddressRequest;
use Ex3mm\Dadata\Requests\SuggestPartyRequest;

/**
 * Интерфейс главного клиента для работы с DaData API.
 */
interface DadataClientInterface
{
    /**
     * Создаёт request builder для стандартизации адресов.
     */
    public function cleanAddress(): CleanAddressRequest;

    /**
     * Создаёт request builder для получения подсказок по адресам.
     */
    public function suggestAddress(): SuggestAddressRequest;

    /**
     * Создаёт request builder для получения подсказок по организациям.
     */
    public function suggestParty(): SuggestPartyRequest;

    /**
     * Создаёт request builder для поиска организаций по ИНН/ОГРН.
     */
    public function findParty(): FindPartyRequest;

    /**
     * Создаёт request builder для произвольных запросов к DaData API.
     */
    public function raw(): RawRequest;
}
