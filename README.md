# ex3mm/dadata

Клиент для DaData Suggestions API на PHP 8.5+.

Пакет рассчитан на два сценария:
- Laravel 12+ (через контейнер и Facade)
- standalone PHP (через фабрику)

## Возможности пакета

- Типизированные DTO-ответы
- Fluent-builder для запроса
- Единая система исключений
- Middleware на уровне HTTP-клиента:
  - retry с exponential backoff
  - ограничение частоты запросов (sliding window)
  - кеширование успешных POST-запросов
  - логирование запросов и ответов

## Установка

```bash
composer require ex3mm/dadata
```

## Быстрый старт (standalone)

```php
<?php

declare(strict_types=1);

use Ex3mm\Dadata\DadataFactory;
use Ex3mm\Dadata\Enums\AddressBound;
use Ex3mm\Dadata\Enums\Language;

require __DIR__ . '/vendor/autoload.php';

$client = DadataFactory::create(
    apiKey: 'your_api_key',
    secretKey: 'your_secret_key'
);

$response = $client->suggestAddress()
    ->query('Москва, Тверская')
    ->count(5)
    ->fromBound(AddressBound::CITY)
    ->toBound(AddressBound::STREET)
    ->language(Language::RU)
    ->get();

foreach ($response->items as $item) {
    echo $item->value . PHP_EOL;
}
```

Пример стандартизации адреса:

```php
<?php

declare(strict_types=1);

use Ex3mm\Dadata\DadataFactory;

$client = DadataFactory::create('your_api_key', 'your_secret_key');

$response = $client->cleanAddress()
    ->query('москва сухонская 11')
    ->get();

$cleaned = $response->items[0] ?? null;
if ($cleaned !== null) {
    echo $cleaned->result . PHP_EOL;
}
```

## Laravel: подключение и использование

### 1) `.env`

Минимально:

```dotenv
DADATA_API_KEY=your_api_key
DADATA_SECRET_KEY=your_secret_key
```

Полный набор переменных:

| Переменная | По умолчанию | Назначение |
|---|---|---|
| `DADATA_API_KEY` | `''` | API-ключ DaData |
| `DADATA_SECRET_KEY` | `''` | Секретный ключ |
| `DADATA_BASE_URL_CLEANER` | `https://cleaner.dadata.ru` | Базовый URL Cleaner API |
| `DADATA_BASE_URL_SUGGESTIONS` | `https://suggestions.dadata.ru` | Базовый URL Suggestions API |
| `DADATA_CONNECT_TIMEOUT` | `10` | Таймаут подключения, сек |
| `DADATA_TIMEOUT` | `30` | Общий таймаут запроса, сек |
| `DADATA_RETRY_ATTEMPTS` | `3` | Количество попыток retry |
| `DADATA_RETRY_DELAY` | `100` | Базовая задержка retry, мс |
| `DADATA_CACHE_ENABLED` | `true` | Включить кеширование |
| `DADATA_CACHE_TTL` | `3600` | TTL кеша, сек |
| `DADATA_CACHE_STORE` | `null` | Имя cache store Laravel |
| `DADATA_LOG_LEVEL` | `info` | Уровень логирования в конфиге пакета |
| `DADATA_LOG_REQUEST_BODY` | `false` | Логировать тело запроса |
| `DADATA_LOG_RESPONSE_BODY` | `false` | Логировать тело ответа |
| `DADATA_LOG_CHANNEL` | `null` | Канал логирования Laravel |
| `DADATA_RATE_LIMIT_ENABLED` | `true` | Включить rate limit |
| `DADATA_RATE_LIMIT` | `20` | Лимит запросов в секунду |

### 2) Публикация конфига (опционально)

```bash
php artisan vendor:publish --tag=dadata-config
```

### 3) Использование через DI

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Ex3mm\Dadata\Contracts\DadataClientInterface;
use Ex3mm\Dadata\Enums\AddressBound;
use Ex3mm\Dadata\Enums\Language;

final class AddressSuggestController
{
    public function __invoke(DadataClientInterface $dadata): array
    {
        $result = $dadata->suggestAddress()
            ->query('Казань')
            ->count(10)
            ->fromBound(AddressBound::CITY)
            ->toBound(AddressBound::STREET)
            ->language(Language::RU)
            ->get();

        return $result->toArray();
    }
}
```

### 4) Использование через Facade

```php
<?php

use Dadata;
use Ex3mm\Dadata\Enums\AddressBound;
use Ex3mm\Dadata\Enums\Language;

$result = Dadata::suggestAddress()
    ->query('Санкт-Петербург')
    ->count(5)
    ->fromBound(AddressBound::CITY)
    ->toBound(AddressBound::STREET)
    ->language(Language::RU)
    ->get();
```

Упрощённый вызов стандартизации адреса:

```php
<?php

use Dadata;

$response = Dadata::getCleanAddress('москва сухонская 11');
$rawItem = $response->items[0] ?? null;
```

### 5) Произвольный запрос через Facade

`getCustom()` возвращает оригинальный body ответа DaData (строка).

```php
<?php

use Dadata;

$rawBody = Dadata::getCustom(
    method: 'POST',
    endpoint: 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address',
    payload: [
        'query' => 'Москва',
        'count' => 5,
    ]
);
```

Пример для Cleaner API (полный URL):

```php
<?php

use Dadata;

$rawBody = Dadata::getCustom(
    method: 'POST',
    endpoint: 'https://cleaner.dadata.ru/api/v1/clean/address',
    payload: ['мск сухонска 11/-89']
);
```

## Endpoint и методы

### Endpoints

| Клиентский метод | HTTP endpoint |
|---|---|
| `suggestAddress()` | `https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address` |
| `suggestBank()` | `https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/bank` |
| `suggestParty()` | `https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party` |
| `findAffiliated()` | `https://suggestions.dadata.ru/suggestions/api/4_1/rs/findAffiliated/party` |
| `findBank()` | `https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/bank` |
| `findParty()` | `https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party` |
| `cleanAddress()` | `https://cleaner.dadata.ru/api/v1/clean/address` |
| `custom()` | Любой полный URL DaData endpoint |

### `suggestAddress()`

Возвращает подсказки адресов по неполному адресу.

Запрос (standalone):
```php
<?php

$response = $client->suggestAddress()
    ->query('москва твер')
    ->count(5)
    ->fromBound(\Ex3mm\Dadata\Enums\AddressBound::CITY)
    ->toBound(\Ex3mm\Dadata\Enums\AddressBound::STREET)
    ->language(\Ex3mm\Dadata\Enums\Language::RU)
    ->locations([['region' => 'Москва']])
    ->get();
```

Запрос через Facade (builder):
```php
<?php

use Dadata;
use Ex3mm\Dadata\Enums\AddressBound;
use Ex3mm\Dadata\Enums\Language;

$response = Dadata::suggestAddress()
    ->query('москва твер')
    ->count(5)
    ->fromBound(AddressBound::CITY)
    ->toBound(AddressBound::STREET)
    ->language(Language::RU)
    ->locations([['region' => 'Москва']])
    ->get();
```

Упрощённый запрос через Facade:
```php
<?php

use Dadata;

$response = Dadata::getSuggestAddress(
    'москва твер'
);
```

Ответ:
```php
Ex3mm\Dadata\DTO\Response\CollectionResponse {
  +items: [
    0 => Ex3mm\Dadata\DTO\Response\Shared\AddressValueDto { ... }
  ],
  +raw: "{\"suggestions\":[...]}",
  +total: 1
}
```

### `cleanAddress()`

Стандартизует один адрес и возвращает нормализованные данные DaData.

Запрос (standalone):
```php
<?php

$response = $client->cleanAddress()
    ->query('москва сухонская 11')
    ->get();
```

Запрос через Facade (builder):
```php
<?php

use Dadata;

$response = Dadata::cleanAddress()
    ->query('москва сухонская 11')
    ->get();
```

Упрощённый запрос через Facade:
```php
<?php

use Dadata;

$response = Dadata::getCleanAddress('москва сухонская 11');
```

Ответ:
```php
Ex3mm\Dadata\DTO\Response\CollectionResponse {
  +items: [
    0 => Ex3mm\Dadata\DTO\Response\CleanAddress\CleanAddressResponseDto { ... }
  ],
  +raw: "[{\"source\":\"москва сухонская 11\",\"result\":\"г Москва, ул Сухонская, д 11\",...}]",
  +total: 1
}
```

### `suggestBank()`

Возвращает подсказки банков по БИК, ИНН, SWIFT, названию или адресу.

Запрос (standalone):
```php
<?php

$response = $client->suggestBank()
    ->query('сбербанк')
    ->count(5)
    ->status(['ACTIVE'])
    ->type(['BANK'])
    ->locations([['region' => 'Москва']])
    ->locationsBoost([['kladr_id' => '7700000000000']])
    ->get();
```

Запрос через Facade (builder):
```php
<?php

use Dadata;

$response = Dadata::suggestBank()
    ->query('сбербанк')
    ->count(5)
    ->status(['ACTIVE'])
    ->type(['BANK'])
    ->locations([['region' => 'Москва']])
    ->locationsBoost([['kladr_id' => '7700000000000']])
    ->get();
```

Упрощённый запрос через Facade:
```php
<?php

use Dadata;

$response = Dadata::getSuggestBank(
    query: 'сбербанк',
    count: 5,
    status: ['ACTIVE'],
    type: ['BANK'],
    locations: [['region' => 'Москва']],
    locationsBoost: [['kladr_id' => '7700000000000']]
);
```

Ответ:
```php
Ex3mm\Dadata\DTO\Response\CollectionResponse {
  +items: [
    0 => Ex3mm\Dadata\DTO\Response\Shared\Bank\BankResponseDto { ... }
  ],
  +raw: "{\"suggestions\":[...]}",
  +total: 1
}
```

### `findBank()`

Находит банк по точному совпадению БИК, SWIFT, ИНН, ИНН+КПП или регистрационного номера.

Запрос (standalone):
```php
<?php

$response = $client->findBank()
    ->query('044525225')
    ->count(1)
    ->get();
```

Запрос через Facade (builder):
```php
<?php

use Dadata;

$response = Dadata::findBank()
    ->query('044525225')
    ->count(1)
    ->get();
```

Упрощённый запрос через Facade:
```php
<?php

use Dadata;

$response = Dadata::getFindBank('044525225');
```

Пример поиска филиала по ИНН + КПП:
```php
<?php

use Dadata;

$response = Dadata::getFindBank(
    query: '7728168971',
    count: 1,
    kpp: '667102002'
);
```

Ответ:
```php
Ex3mm\Dadata\DTO\Response\CollectionResponse {
  +items: [
    0 => Ex3mm\Dadata\DTO\Response\Shared\Bank\BankResponseDto { ... }
  ],
  +raw: "{\"suggestions\":[...]}",
  +total: 1
}
```

### `findParty()`

Находит организацию по точному совпадению ИНН, ИНН+КПП или ОГРН.

Запрос (standalone):
```php
<?php

use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyStateStatus;
use Ex3mm\Dadata\Enums\PartyType;

$response = $client->findParty()
    ->query('7707083893')
    ->count(1)
    ->branchType(PartyBranchType::MAIN)
    ->type(PartyType::LEGAL)
    ->status([PartyStateStatus::ACTIVE])
    ->get();
```

Запрос через Facade (builder):
```php
<?php

use Dadata;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyStateStatus;
use Ex3mm\Dadata\Enums\PartyType;

$response = Dadata::findParty()
    ->query('7707083893')
    ->count(1)
    ->branchType(PartyBranchType::MAIN)
    ->type(PartyType::LEGAL)
    ->status([PartyStateStatus::ACTIVE])
    ->get();
```

Упрощённый запрос через Facade:
```php
<?php

use Dadata;

$response = Dadata::getFindParty('7707083893');
```

Пример поиска филиала по ИНН + КПП:
```php
<?php

use Dadata;

$response = Dadata::getFindParty(
    query: '7701234567',
    count: 1,
    kpp: '770101001'
);
```

Пример с фильтрами:
```php
<?php

use Dadata;
use Ex3mm\Dadata\Enums\PartyBranchType;
use Ex3mm\Dadata\Enums\PartyStateStatus;
use Ex3mm\Dadata\Enums\PartyType;

$response = Dadata::getFindParty(
    query: '7701234567',
    count: 10,
    branchType: PartyBranchType::BRANCH,
    type: PartyType::LEGAL,
    status: [PartyStateStatus::ACTIVE]
);
```

Ответ:
```php
Ex3mm\Dadata\DTO\Response\CollectionResponse {
  +items: [
    0 => Ex3mm\Dadata\DTO\Response\Shared\Party\PartyResponseDto { ... }
  ],
  +raw: "{\"suggestions\":[...]}",
  +total: 1
}
```

### `findAffiliated()`

Находит аффилированные компании по ИНН учредителя или руководителя.

Запрос (standalone):
```php
<?php

use Ex3mm\Dadata\Enums\AffiliatedScope;

$response = $client->findAffiliated()
    ->query('7736207543')
    ->count(10)
    ->scope([AffiliatedScope::FOUNDERS, AffiliatedScope::MANAGERS])
    ->get();
```

Запрос через Facade (builder):
```php
<?php

use Dadata;
use Ex3mm\Dadata\Enums\AffiliatedScope;

$response = Dadata::findAffiliated()
    ->query('7736207543')
    ->count(10)
    ->scope([AffiliatedScope::FOUNDERS])
    ->get();
```

Упрощённый запрос через Facade:
```php
<?php

use Dadata;

$response = Dadata::getFindAffiliated('7736207543');
```

Ответ:
```php
Ex3mm\Dadata\DTO\Response\CollectionResponse {
  +items: [
    0 => Ex3mm\Dadata\DTO\Response\Shared\Party\AffiliatedPartyResponseDto { ... }
  ],
  +raw: "{\"suggestions\":[...]}",
  +total: 1
}
```

### `suggestParty()`

Возвращает подсказки по организациям (юрлица и ИП) по ИНН, ОГРН, названию, ФИО руководителя или адресу.

Запрос (standalone):
```php
<?php

use Ex3mm\Dadata\Enums\PartyStateStatus;
use Ex3mm\Dadata\Enums\PartyType;

$response = $client->suggestParty()
    ->query('сбербанк')
    ->count(5)
    ->type(PartyType::LEGAL)
    ->status([PartyStateStatus::ACTIVE])
    ->okved(['64.19'])
    ->locations([['region' => 'Москва']])
    ->get();
```

Запрос через Facade (builder):
```php
<?php

use Dadata;
use Ex3mm\Dadata\Enums\PartyType;

$response = Dadata::suggestParty()
    ->query('сбербанк')
    ->count(5)
    ->type(PartyType::LEGAL)
    ->get();
```

Упрощённый запрос через Facade:
```php
<?php

use Dadata;

$response = Dadata::getSuggestParty('сбербанк');
```

Ответ:
```php
Ex3mm\Dadata\DTO\Response\CollectionResponse {
  +items: [
    0 => Ex3mm\Dadata\DTO\Response\Shared\Party\PartyResponseDto { ... }
  ],
  +raw: "{\"suggestions\":[...]}",
  +total: 1
}
```

### `custom()`

Выполняет произвольный запрос к DaData endpoint и возвращает raw body.

Запрос (standalone):
```php
<?php

$rawBody = $client->custom()
    ->method('POST')
    ->url('https://cleaner.dadata.ru/api/v1/clean/address')
    ->json(['мск сухонска 11/-89'])
    ->get();
```

Запрос через Facade (builder):
```php
<?php

use Dadata;

$rawBody = Dadata::custom()
    ->method('POST')
    ->url('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address')
    ->json(['query' => 'Москва', 'count' => 3])
    ->get();
```

Упрощённый запрос через Facade:
```php
<?php

use Dadata;

$rawBody = Dadata::getCustom(
    method: 'POST',
    endpoint: 'https://cleaner.dadata.ru/api/v1/clean/address',
    payload: ['мск сухонска 11/-89']
);
```

Ответ:
```php
"[{\"source\":\"мск сухонска 11/-89\",...}]"
```

### `SuggestAddressRequest`

| Метод | Что делает |
|---|---|
| `query(string $query)` | Устанавливает поисковый запрос (обязательный параметр) |
| `count(int $count)` | Количество подсказок |
| `fromBound(AddressBound $bound)` | Нижняя граница детализации: `AddressBound::...` |
| `toBound(AddressBound $bound)` | Верхняя граница детализации: `AddressBound::...` |
| `language(Language $language)` | Язык ответа: `Language::RU` или `Language::EN` |
| `division(string $division)` | Тип деления (`administrative` / `municipal`) |
| `locations(array $locations)` | Фильтр по родительским локациям |
| `locationsGeo(array $locationsGeo)` | Гео-фильтр по радиусу |
| `locationsBoost(array $locationsBoost)` | Приоритет локаций при ранжировании |
| `get()` | Выполняет запрос, возвращает `CollectionResponse<AddressValueDto>` |

### `SuggestBankRequest`

| Метод | Что делает |
|---|---|
| `query(string $query)` | Устанавливает поисковый запрос (обязательный параметр) |
| `count(int $count)` | Количество подсказок |
| `status(array $status)` | Ограничение по статусу банка |
| `type(array $type)` | Ограничение по типу банка |
| `locations(array $locations)` | Ограничение по региону или городу |
| `locationsBoost(array $locationsBoost)` | Приоритет города при ранжировании |
| `get()` | Выполняет запрос, возвращает `CollectionResponse<BankResponseDto>` |

### `SuggestPartyRequest`

| Метод | Что делает |
|---|---|
| `query(string $query)` | Устанавливает поисковый запрос (обязательный параметр) |
| `count(int $count)` | Количество подсказок |
| `type(PartyType $type)` | Ограничение по типу организации: `PartyType::LEGAL`, `PartyType::INDIVIDUAL` |
| `status(array $status)` | Ограничение по статусу: массив `PartyStateStatus::...` |
| `okved(array $okved)` | Фильтр по кодам ОКВЭД |
| `locations(array $locations)` | Ограничение по региону или городу |
| `locationsBoost(array $locationsBoost)` | Приоритет города при ранжировании |
| `get()` | Выполняет запрос, возвращает `CollectionResponse<PartyResponseDto>` |

### `FindBankRequest`

| Метод | Что делает |
|---|---|
| `query(string $query)` | Идентификатор банка для точного поиска (БИК/SWIFT/ИНН/рег. номер) |
| `count(int $count)` | Количество результатов |
| `kpp(string $kpp)` | КПП (используется вместе с ИНН для филиалов) |
| `get()` | Выполняет запрос, возвращает `CollectionResponse<BankResponseDto>` |

### `FindPartyRequest`

| Метод | Что делает |
|---|---|
| `query(string $query)` | ИНН или ОГРН организации для точного поиска |
| `count(int $count)` | Количество результатов (максимум 300) |
| `kpp(string $kpp)` | КПП (используется вместе с ИНН для филиалов) |
| `branchType(PartyBranchType $branchType)` | Фильтр по типу подразделения: `PartyBranchType::MAIN`, `PartyBranchType::BRANCH` |
| `type(PartyType $type)` | Фильтр по типу организации: `PartyType::LEGAL`, `PartyType::INDIVIDUAL` |
| `status(array $status)` | Фильтр по статусу: массив `PartyStateStatus::...` |
| `get()` | Выполняет запрос, возвращает `CollectionResponse<PartyResponseDto>` |

### `FindAffiliatedRequest`

| Метод | Что делает |
|---|---|
| `query(string $query)` | ИНН учредителя или руководителя |
| `count(int $count)` | Количество результатов (максимум 300) |
| `scope(array $scope)` | Область поиска: массив `AffiliatedScope::FOUNDERS`, `AffiliatedScope::MANAGERS` |
| `get()` | Выполняет запрос, возвращает `CollectionResponse<AffiliatedPartyResponseDto>` |

### `CleanAddressRequest`

| Метод | Что делает |
|---|---|
| `query(string $query)` | Устанавливает адрес для стандартизации (обязательный параметр) |
| `get()` | Выполняет запрос, возвращает `CollectionResponse<CleanAddressResponseDto>` |

Ограничение DaData для `clean/address`: в одном запросе обрабатывается один адрес.

## Enums проекта

Ниже перечислены все enum, которые используются в пакете.

### `AddressBound`

Где используется:
- `SuggestAddressRequest::fromBound()`
- `SuggestAddressRequest::toBound()`

| Значение | Описание |
|---|---|
| `COUNTRY` (`country`) | Страна |
| `REGION` (`region`) | Регион |
| `AREA` (`area`) | Район |
| `CITY` (`city`) | Город |
| `CITY_DISTRICT` (`city_district`) | Район города |
| `SETTLEMENT` (`settlement`) | Населённый пункт |
| `STREET` (`street`) | Улица |
| `HOUSE` (`house`) | Дом |
| `FLAT` (`flat`) | Квартира |

### `Language`

Где используется:
- `SuggestAddressRequest::language()`

| Значение | Описание |
|---|---|
| `RU` (`ru`) | Русский язык |
| `EN` (`en`) | Английский язык |

### `AddressFiasLevel`

Соответствует значению поля:
- `AddressDataDto::$fiasLevel` (`data.fias_level` в ответе `suggestAddress`)

| Значение | Код | Описание |
|---|---:|---|
| `UNKNOWN` | `-1` | Не определён |
| `COUNTRY` | `0` | Страна |
| `REGION` | `1` | Регион |
| `AREA` | `3` | Район |
| `CITY` | `4` | Город |
| `CITY_DISTRICT` | `5` | Район города |
| `SETTLEMENT` | `6` | Населённый пункт |
| `STREET` | `7` | Улица |
| `HOUSE` | `8` | Дом |
| `FLAT` | `9` | Квартира |

### `BankOpfType`

Где используется:
- `BankOpfDto::$type` (`data.opf.type` в `suggestBank` и `findBank`)

| Значение | Описание |
|---|---|
| `BANK` | Банк |
| `BANK_BRANCH` | Филиал банка |
| `NKO` | Небанковская кредитная организация |
| `NKO_BRANCH` | Филиал небанковской кредитной организации |
| `RKC` | Расчетно-кассовый центр |
| `CBR` | Управление ЦБ РФ |
| `TREASURY` | Управление Казначейства |
| `OTHER` | Другой тип кредитной организации |

### `BankStateStatus`

Где используется:
- `BankStateDto::$status` (`data.state.status` в `suggestBank` и `findBank`)

| Значение | Описание |
|---|---|
| `ACTIVE` | Действующая организация |
| `LIQUIDATING` | Организация в процессе ликвидации |
| `LIQUIDATED` | Ликвидированная организация |

### `AffiliatedScope`

Где используется:
- `FindAffiliatedRequest::scope()`

| Значение | Описание |
|---|---|
| `FOUNDERS` | Искать среди учредителей |
| `MANAGERS` | Искать среди руководителей |

### `PartyType`

Где используется:
- `AffiliatedPartyDataDto::$type` (`data.type` в `findAffiliated`)
- `PartyDataDto::$type` (`data.type` в `suggestParty`, `findParty`)

| Значение | Описание |
|---|---|
| `LEGAL` | Юридическое лицо |
| `INDIVIDUAL` | Индивидуальный предприниматель |

### `PartyBranchType`

Где используется:
- `AffiliatedPartyDataDto::$branchType` (`data.branch_type` в `findAffiliated`)
- `PartyDataDto::$branchType` (`data.branch_type` в `suggestParty`, `findParty`)

| Значение | Описание |
|---|---|
| `MAIN` | Головная организация |
| `BRANCH` | Филиал |

### `PartyStateStatus`

Где используется:
- `PartyStateDto::$status` (`data.state.status` в `findAffiliated`)
- `PartyStateDto::$status` (`data.state.status` в `suggestParty`, `findParty`)

| Значение | Описание |
|---|---|
| `ACTIVE` | Действующая организация |
| `LIQUIDATING` | Организация в процессе ликвидации |
| `LIQUIDATED` | Ликвидированная организация |
| `BANKRUPT` | Организация в процедуре банкротства |
| `REORGANIZING` | Организация в процессе реорганизации |

## Формат ответа

## Структура DTO

Endpoint-специфичные DTO:
- `CleanAddress\CleanAddressResponseDto` — только `cleanAddress`

Общие DTO (`Shared`), используемые несколькими endpoint:
- `Shared\AddressDataDto` — используется в `suggestAddress`, `cleanAddress`, `suggestBank`, `findBank`, `findAffiliated`, `suggestParty`, `findParty`
- `Shared\AddressValueDto` — используется в `suggestAddress`, `suggestBank`, `findBank`, `findAffiliated`, `suggestParty`, `findParty`
- `Shared\Bank\BankResponseDto` — используется в `suggestBank`, `findBank`
- `Shared\Bank\BankDataDto` — используется в `suggestBank`, `findBank`
- `Shared\Bank\BankOpfDto` — используется в `suggestBank`, `findBank`
- `Shared\Bank\BankNameDto` — используется в `suggestBank`, `findBank`
- `Shared\Bank\BankStateDto` — используется в `suggestBank`, `findBank`
- `Shared\Party\PartyResponseDto` — используется в `suggestParty`, `findParty`
- `Shared\Party\PartyDataDto` — используется в `suggestParty`, `findParty`
- `Shared\Party\PartyNameDto` — используется в `suggestParty`, `findParty`
- `Shared\Party\PartyOpfDto` — используется в `suggestParty`, `findParty`
- `Shared\Party\PartyManagementDto` — используется в `suggestParty`, `findParty`
- `Shared\Party\AffiliatedPartyResponseDto` — используется в `findAffiliated`
- `Shared\Party\AffiliatedPartyDataDto` — используется в `findAffiliated`
- `Shared\Party\PartyStateDto` — используется в `findAffiliated`, `suggestParty`, `findParty`

### `CollectionResponse<T>`

| Поле | Тип | Описание |
|---|---|---|
| `items` | `list<T>` | Коллекция DTO |
| `raw` | `string` | Сырой JSON ответа |
| `total` | `int` | Количество элементов в `items` |

### `AddressValueDto`

| Поле | Тип | Описание |
|---|---|---|
| `value` | `string` | Короткий вариант адреса |
| `unrestrictedValue` | `string` | Полный вариант адреса |
| `data` | `AddressDataDto` | Детализация от DaData |

`AddressDataDto` — общий DTO для адресных данных в ответах (`suggestAddress`, `cleanAddress`, `suggestBank`, `findBank`, `findAffiliated`, `suggestParty`, `findParty`).
Актуальный namespace: `Ex3mm\Dadata\DTO\Response\Shared\AddressDataDto`.

## Логирование, кеш, retry, rate-limit

### Логирование

- Логируется факт запроса: метод + URL
- Логируется ответ: статус + длительность
- По флагам можно писать тело запроса/ответа:
  - `log_request_body`
  - `log_response_body`
- При включённом кеше в логах виден признак `X-Kevinrob-Cache` (`HIT`/`MISS`)

### Кеширование

- Кешируются только успешные `POST`-ответы (2xx)
- Ключ кеша: URL + тело запроса
- TTL задаётся через `cache_ttl`
- Laravel: используется `cache_store` или store по умолчанию
- Standalone: по умолчанию in-memory cache

### Retry

Повторы выполняются для:
- HTTP: `429`, `500`, `502`, `503`, `504`
- сетевых ошибок подключения

Параметры:
- `retry_attempts`
- `retry_delay` (с дальнейшим экспоненциальным увеличением)

### Ограничение частоты запросов (sliding window)

- Лимит считается по плавающему окну в 1 секунду
- Если в последнюю секунду уже достигнут `rate_limit`, выбрасывается `RateLimitException`
- В отличие от fixed-window, нет "всплеска" на границе секунд

## Конфигурация standalone

```php
<?php

declare(strict_types=1);

use Ex3mm\Dadata\DadataFactory;

$client = DadataFactory::create(
    apiKey: 'your_api_key',
    secretKey: 'your_secret_key',
    options: [
        'timeout' => 10,
        'retry_attempts' => 2,
        'retry_delay' => 150,
        'cache_enabled' => true,
        'cache_ttl' => 600,
        'rate_limit_enabled' => true,
        'rate_limit' => 15,
        'log_request_body' => false,
        'log_response_body' => false,
    ]
);
```

## Исключения и обработка ошибок

| Исключение | Сценарий |
|---|---|
| `ConfigurationException` | Невалидная конфигурация |
| `ValidationException` | Ошибка в параметрах запроса или структуре ответа |
| `AuthenticationException` | Ошибки авторизации (`401`, `403`) |
| `RateLimitException` | Превышен лимит запросов (`429`) |
| `ApiException` | Прочие API-ошибки (`4xx/5xx`) |
| `NetworkException` | Сетевые ошибки |

Пример:

```php
<?php

use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Exceptions\RateLimitException;
use Ex3mm\Dadata\Exceptions\ValidationException;

try {
    $result = $client->suggestAddress()->query('Москва')->get();
} catch (RateLimitException $e) {
    // Можно повторить позже
} catch (ValidationException $e) {
    // Ошибка параметров запроса
} catch (DadataException $e) {
    // Любая другая ошибка пакета
}
```

## Лицензия

MIT. Подробности: [LICENSE](LICENSE)
