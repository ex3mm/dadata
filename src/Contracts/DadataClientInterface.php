<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\Contracts;

use Ex3mm\Dadata\Requests\CleanAddressRequest;
use Ex3mm\Dadata\Requests\CustomRequest;
use Ex3mm\Dadata\Requests\FindAffiliatedRequest;
use Ex3mm\Dadata\Requests\FindBankRequest;
use Ex3mm\Dadata\Requests\FindPartyRequest;
use Ex3mm\Dadata\Requests\SuggestAddressRequest;
use Ex3mm\Dadata\Requests\SuggestBankRequest;
use Ex3mm\Dadata\Requests\SuggestPartyRequest;

/**
 * Интерфейс главного клиента для работы с DaData API.
 */
interface DadataClientInterface
{
    /**
     * Создаёт request builder для получения подсказок по адресам.
     */
    public function suggestAddress(): SuggestAddressRequest;

    /**
     * Создаёт request builder для получения подсказок по банкам.
     */
    public function suggestBank(): SuggestBankRequest;

    /**
     * Создаёт request builder для подсказок по организациям.
     */
    public function suggestParty(): SuggestPartyRequest;

    /**
     * Создаёт request builder для поиска аффилированных компаний.
     */
    public function findAffiliated(): FindAffiliatedRequest;

    /**
     * Создаёт request builder для поиска банка по БИК, SWIFT, ИНН или регистрационному номеру.
     */
    public function findBank(): FindBankRequest;

    /**
     * Создаёт request builder для поиска организации по ИНН или ОГРН.
     */
    public function findParty(): FindPartyRequest;

    /**
     * Создаёт request builder для стандартизации адреса.
     */
    public function cleanAddress(): CleanAddressRequest;

    /**
     * Создаёт request builder для произвольных запросов к DaData API.
     */
    public function custom(): CustomRequest;
}
