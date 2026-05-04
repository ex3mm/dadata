# ex3mm/dadata

PHP-клиент для DaData API. Поддерживает Laravel 12+ и standalone PHP 8.5+.

## Возможности

- Типизированные DTO-ответы
- Fluent-builder для построения запросов
- Единая иерархия исключений
- HTTP middleware: retry с exponential backoff, sliding window rate limit, кеширование POST-ответов, логирование

## Требования

- PHP 8.5+
- Laravel 12+ (для Laravel-интеграции)

## Установка

```bash
composer require ex3mm/dadata
```

---

## Быстрый старт

### Standalone

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

// Подсказки адресов
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

// Стандартизация адреса
$response = $client->cleanAddress()
    ->query('москва сухонская 11')
    ->get();

echo $response->items[0]->result . PHP_EOL;
```

### Laravel

Минимальная конфигурация `.env`:

```dotenv
DADATA_API_KEY=your_api_key
DADATA_SECRET_KEY=your_secret_key
```

Использование через DI:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Ex3mm\Dadata\Contracts\DadataClientInterface;

final class AddressSuggestController
{
    public function __invoke(DadataClientInterface $dadata): array
    {
        return $dadata->suggestAddress()
            ->query('Казань')
            ->count(10)
            ->get()
            ->toArray();
    }
}
```

Использование через Facade:

```php
use Dadata;

$response = Dadata::suggestAddress()
    ->query('Санкт-Петербург')
    ->count(5)
    ->get();

// Упрощённые методы доступны для всех endpoints
$response = Dadata::getSuggestAddress('Санкт-Петербург');
$response = Dadata::getCleanAddress('москва сухонская 11');
$response = Dadata::getSuggestBank('сбербанк');
$response = Dadata::getSuggestParty('сбербанк');
$response = Dadata::getSuggestFio('Викт');
$response = Dadata::getFindBank('044525225');
$response = Dadata::getFindParty('7707083893');
$response = Dadata::getFindAffiliated('7736207543');
```

Публикация конфига (опционально):

```bash
php artisan vendor:publish --tag=dadata-config
```

---

## Endpoints

| Метод | Описание | HTTP endpoint |
|---|---|---|
| `suggestAddress()` | Подсказки адресов | `.../suggest/address` |
| `cleanAddress()` | Стандартизация адреса | `.../clean/address` |
| `suggestBank()` | Подсказки банков | `.../suggest/bank` |
| `findBank()` | Поиск банка по БИК/ИНН/SWIFT | `.../findById/bank` |
| `suggestParty()` | Подсказки организаций | `.../suggest/party` |
| `findParty()` | Поиск организации по ИНН/ОГРН | `.../findById/party` |
| `findAffiliated()` | Аффилированные компании по ИНН | `.../findAffiliated/party` |
| `suggestFio()` | Подсказки ФИО | `.../suggest/fio` |
| `custom()` | Произвольный запрос к DaData | любой URL |

Базовые URL: `https://suggestions.dadata.ru/suggestions/api/4_1/rs` и `https://cleaner.dadata.ru/api/v1`.

---

## Методы запросов

### `suggestAddress()`

Подсказки по неполному адресу.

```php
$response = $client->suggestAddress()
    ->query('москва твер')           // обязательный
    ->count(5)
    ->fromBound(AddressBound::CITY)
    ->toBound(AddressBound::STREET)
    ->language(Language::RU)
    ->locations([['region' => 'Москва']])
    ->locationsGeo([['lat' => 55.878, 'lon' => 37.653, 'radius_meters' => 5000]])
    ->locationsBoost([['kladr_id' => '7700000000000']])
    ->get();
```

| Метод | Описание |
|---|---|
| `query(string)` | Поисковый запрос (обязательный) |
| `count(int)` | Количество подсказок |
| `fromBound(AddressBound)` | Нижняя граница детализации |
| `toBound(AddressBound)` | Верхняя граница детализации |
| `language(Language)` | Язык ответа: `RU` или `EN` |
| `division(string)` | Тип деления: `administrative` / `municipal` |
| `locations(array)` | Фильтр по родительским локациям |
| `locationsGeo(array)` | Гео-фильтр по радиусу |
| `locationsBoost(array)` | Приоритет локаций при ранжировании |

Возвращает `CollectionResponse<AddressValueDto>`.

---

### `cleanAddress()`

Стандартизация одного адреса. DaData обрабатывает один адрес за запрос.

```php
$response = $client->cleanAddress()
    ->query('москва сухонская 11')   // обязательный
    ->get();

echo $response->items[0]->result;
// "г Москва, ул Сухонская, д 11"
```

Возвращает `CollectionResponse<CleanAddressResponseDto>`.

---

### `suggestBank()`

Подсказки банков по БИК, ИНН, SWIFT, названию или адресу.

```php
$response = $client->suggestBank()
    ->query('сбербанк')              // обязательный
    ->count(5)
    ->status(['ACTIVE'])
    ->type(['BANK'])
    ->locations([['region' => 'Москва']])
    ->locationsBoost([['kladr_id' => '7700000000000']])
    ->get();
```

| Метод | Описание |
|---|---|
| `query(string)` | Поисковый запрос (обязательный) |
| `count(int)` | Количество подсказок |
| `status(array)` | Фильтр по статусу банка, `list<string>`: например `['ACTIVE']` |
| `type(array)` | Фильтр по типу банка, `list<string>`: например `['BANK']` |
| `locations(array)` | Фильтр по региону или городу |
| `locationsBoost(array)` | Приоритет города при ранжировании |

Возвращает `CollectionResponse<BankResponseDto>`.

---

### `findBank()`

Точный поиск банка по БИК, SWIFT, ИНН или регистрационному номеру.

```php
// По БИК
$response = $client->findBank()
    ->query('044525225')
    ->count(1)
    ->get();

// По ИНН + КПП (для филиалов)
$response = $client->findBank()
    ->query('7728168971')
    ->kpp('667102002')
    ->count(1)
    ->get();
```

| Метод | Описание |
|---|---|
| `query(string)` | БИК, SWIFT, ИНН или рег. номер (обязательный) |
| `count(int)` | Количество результатов |
| `kpp(string)` | КПП — используется вместе с ИНН для поиска филиала |

Возвращает `CollectionResponse<BankResponseDto>`.

---

### `suggestParty()`

Подсказки организаций (юрлица и ИП) по ИНН, ОГРН, названию, ФИО руководителя или адресу.

```php
$response = $client->suggestParty()
    ->query('сбербанк')              // обязательный
    ->count(5)
    ->type(PartyType::LEGAL)
    ->status([PartyStateStatus::ACTIVE])
    ->okved(['64.19'])
    ->locations([['region' => 'Москва']])
    ->locationsBoost([['kladr_id' => '7700000000000']])
    ->get();
```

| Метод | Описание |
|---|---|
| `query(string)` | Поисковый запрос (обязательный) |
| `count(int)` | Количество подсказок |
| `type(PartyType)` | Тип: `LEGAL` — юрлицо, `INDIVIDUAL` — ИП |
| `status(array)` | Фильтр по статусу: `PartyStateStatus::...` |
| `okved(array)` | Фильтр по кодам ОКВЭД |
| `locations(array)` | Фильтр по региону или городу |
| `locationsBoost(array)` | Приоритет города при ранжировании |

Возвращает `CollectionResponse<PartyResponseDto>`.

---

### `findParty()`

Точный поиск организации по ИНН или ОГРН. Максимум 300 результатов.

```php
// По ИНН
$response = $client->findParty()
    ->query('7707083893')
    ->count(1)
    ->branchType(PartyBranchType::MAIN)
    ->type(PartyType::LEGAL)
    ->status([PartyStateStatus::ACTIVE])
    ->get();

// Филиал по ИНН + КПП
$response = $client->findParty()
    ->query('7701234567')
    ->kpp('770101001')
    ->count(1)
    ->get();
```

| Метод | Описание |
|---|---|
| `query(string)` | ИНН или ОГРН (обязательный) |
| `count(int)` | Количество результатов (макс. 300) |
| `kpp(string)` | КПП — для поиска конкретного филиала |
| `branchType(PartyBranchType)` | `MAIN` — головная, `BRANCH` — филиал |
| `type(PartyType)` | `LEGAL` — юрлицо, `INDIVIDUAL` — ИП |
| `status(array)` | Фильтр по статусу: `PartyStateStatus::...` |

Возвращает `CollectionResponse<PartyResponseDto>`.

---

### `findAffiliated()`

Поиск аффилированных компаний по ИНН учредителя или руководителя.

```php
$response = $client->findAffiliated()
    ->query('7736207543')            // обязательный
    ->count(10)
    ->scope([AffiliatedScope::FOUNDERS, AffiliatedScope::MANAGERS])
    ->get();
```

| Метод | Описание |
|---|---|
| `query(string)` | ИНН учредителя или руководителя (обязательный) |
| `count(int)` | Количество результатов (фактический предел определяется API DaData) |
| `scope(array)` | `AffiliatedScope::FOUNDERS` и/или `AffiliatedScope::MANAGERS` |

Возвращает `CollectionResponse<AffiliatedPartyResponseDto>`.

---

### `suggestFio()`

Подсказки ФИО по неполному имени, фамилии или отчеству.

```php
$response = $client->suggestFio()
    ->query('Викт')                  // обязательный
    ->count(5)
    ->parts(['NAME'])
    ->gender(Gender::MALE)
    ->get();

foreach ($response->items as $item) {
    echo $item->value . PHP_EOL;
    // Виктор
}
```

| Метод | Описание |
|---|---|
| `query(string)` | Поисковый запрос (обязательный) |
| `count(int)` | Количество подсказок |
| `parts(array)` | Части ФИО для подсказок: `['SURNAME']`, `['NAME']`, `['PATRONYMIC']` или их комбинация |
| `gender(Gender)` | Пол: `Gender::MALE` или `Gender::FEMALE` |

Возвращает `CollectionResponse<FioSuggestionResponseDto>`.

---

### `custom()`

Произвольный запрос к любому DaData endpoint. Возвращает сырой JSON-ответ строкой.

```php
$rawBody = $client->custom()
    ->method('POST')
    ->url('https://cleaner.dadata.ru/api/v1/clean/address')
    ->json(['мск сухонска 11/-89'])
    ->get();
```

Через Facade: `Dadata::getCustom(method: 'POST', endpoint: '...', payload: [...])`.

---

## Формат ответа

Все методы (кроме `custom()`) возвращают `CollectionResponse<T>`:

| Поле | Тип | Описание |
|---|---|---|
| `items` | `list<T>` | Коллекция DTO |
| `raw` | `string` | Сырой JSON ответа |
| `total` | `int` | Количество элементов в `items` |

### `AddressValueDto`

Возвращается из `suggestAddress()`, а также вложен в ответы банков и организаций.

| Поле | Тип | Описание |
|---|---|---|
| `value` | `string` | Короткий вариант адреса |
| `unrestrictedValue` | `string` | Полный вариант адреса |
| `data` | `AddressDataDto` | Детализация (ФИАС, координаты, почтовый индекс и др.) |

---

## Enums

### `AddressBound`
Используется в `fromBound()` / `toBound()` запроса `suggestAddress`.

| Значение | API value | Описание |
|---|---|---|
| `COUNTRY` | `country` | Страна |
| `REGION` | `region` | Регион |
| `AREA` | `area` | Район |
| `CITY` | `city` | Город |
| `CITY_DISTRICT` | `city_district` | Район города |
| `SETTLEMENT` | `settlement` | Населённый пункт |
| `STREET` | `street` | Улица |
| `HOUSE` | `house` | Дом |
| `FLAT` | `flat` | Квартира |

### `Language`
Используется в `language()` запроса `suggestAddress`.

| Значение | API value | Описание |
|---|---|---|
| `RU` | `ru` | Русский |
| `EN` | `en` | Английский |

### `AddressFiasLevel`
Поле `data.fias_level` в ответе `suggestAddress`.

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
Поле `data.opf.type` в ответах `suggestBank` / `findBank`.

| Значение | API value | Описание |
|---|---|---|
| `BANK` | `BANK` | Банк |
| `BANK_BRANCH` | `BANK_BRANCH` | Филиал банка |
| `NKO` | `NKO` | Небанковская кредитная организация |
| `NKO_BRANCH` | `NKO_BRANCH` | Филиал НКО |
| `RKC` | `RKC` | Расчётно-кассовый центр |
| `CBR` | `CBR` | Подразделение ЦБ РФ |
| `TREASURY` | `TREASURY` | Подразделение Казначейства |
| `OTHER` | `OTHER` | Другой тип кредитной организации |

### `BankStateStatus`
Поле `data.state.status` в ответах `suggestBank` / `findBank`.

| Значение | API value | Описание |
|---|---|---|
| `ACTIVE` | `ACTIVE` | Действующая организация |
| `LIQUIDATING` | `LIQUIDATING` | В процессе ликвидации |
| `LIQUIDATED` | `LIQUIDATED` | Ликвидированная организация |

### `PartyType`
Поле `data.type` в ответах `suggestParty` / `findParty` / `findAffiliated`.

| Значение | API value | Описание |
|---|---|---|
| `LEGAL` | `LEGAL` | Юридическое лицо |
| `INDIVIDUAL` | `INDIVIDUAL` | Индивидуальный предприниматель |

### `PartyBranchType`
Поле `data.branch_type` в ответах `suggestParty` / `findParty` / `findAffiliated`.

| Значение | API value | Описание |
|---|---|---|
| `MAIN` | `MAIN` | Головная организация |
| `BRANCH` | `BRANCH` | Филиал |

### `PartyStateStatus`
Поле `data.state.status` в ответах `suggestParty` / `findParty` / `findAffiliated`.

| Значение | API value | Описание |
|---|---|---|
| `ACTIVE` | `ACTIVE` | Действующая организация |
| `LIQUIDATING` | `LIQUIDATING` | В процессе ликвидации |
| `LIQUIDATED` | `LIQUIDATED` | Ликвидированная организация |
| `BANKRUPT` | `BANKRUPT` | В процедуре банкротства |
| `REORGANIZING` | `REORGANIZING` | В процессе реорганизации |

### `Gender`
Используется в `gender()` запроса `suggestFio` и поле `data.gender` в ответах.

| Значение | API value | Описание |
|---|---|---|
| `MALE` | `MALE` | Мужской |
| `FEMALE` | `FEMALE` | Женский |
| `UNKNOWN` | `UNKNOWN` | Не определён |

### `AffiliatedScope`
Используется в `scope()` запроса `findAffiliated`.

| Значение | API value | Описание |
|---|---|---|
| `FOUNDERS` | `FOUNDERS` | Искать среди учредителей |
| `MANAGERS` | `MANAGERS` | Искать среди руководителей |

---

## Конфигурация

### Laravel `.env`

| Переменная | По умолчанию | Описание |
|---|---|---|
| `DADATA_API_KEY` | `''` | API-ключ DaData |
| `DADATA_SECRET_KEY` | `''` | Секретный ключ |
| `DADATA_BASE_URL_CLEANER` | `https://cleaner.dadata.ru` | Базовый URL Cleaner API |
| `DADATA_BASE_URL_SUGGESTIONS` | `https://suggestions.dadata.ru` | Базовый URL Suggestions API |
| `DADATA_CONNECT_TIMEOUT` | `10` | Таймаут подключения, сек |
| `DADATA_TIMEOUT` | `30` | Таймаут запроса, сек |
| `DADATA_RETRY_ATTEMPTS` | `3` | Количество попыток retry |
| `DADATA_RETRY_DELAY` | `100` | Базовая задержка retry, мс |
| `DADATA_CACHE_ENABLED` | `true` | Включить кеширование |
| `DADATA_CACHE_TTL` | `3600` | TTL кеша, сек |
| `DADATA_CACHE_STORE` | `null` | Cache store Laravel |
| `DADATA_LOG_LEVEL` | `info` | Уровень логирования |
| `DADATA_LOG_REQUEST_BODY` | `false` | Логировать тело запроса |
| `DADATA_LOG_RESPONSE_BODY` | `false` | Логировать тело ответа |
| `DADATA_LOG_CHANNEL` | `null` | Канал логирования Laravel |
| `DADATA_RATE_LIMIT_ENABLED` | `true` | Включить rate limit |
| `DADATA_RATE_LIMIT` | `20` | Лимит запросов в секунду |

### Standalone

```php
$client = DadataFactory::create(
    apiKey: 'your_api_key',
    secretKey: 'your_secret_key',
    options: [
        'timeout'            => 10,
        'retry_attempts'     => 2,
        'retry_delay'        => 150,
        'cache_enabled'      => true,
        'cache_ttl'          => 600,
        'rate_limit_enabled' => true,
        'rate_limit'         => 15,
        'log_request_body'   => false,
        'log_response_body'  => false,
    ]
);
```

---

## Middleware

### Retry

Автоматические повторы при HTTP `429`, `500`, `502`, `503`, `504` и сетевых ошибках. Задержка между попытками растёт экспоненциально от значения `retry_delay`.

### Rate limit (sliding window)

Лимит считается по скользящему окну в 1 секунду. При превышении выбрасывается `RateLimitException`. Sliding window исключает всплески на границе секунд, характерные для fixed-window подхода.

### Кеширование

Кешируются только успешные `POST`-ответы (2xx). Ключ кеша: URL + тело запроса. В standalone используется in-memory кеш — не подходит для многопроцессного окружения (PHP-FPM с несколькими воркерами). Для production в standalone-сценарии задайте внешний адаптер или используйте Laravel с настроенным `cache_store`.

### Логирование

Логируется метод, URL, статус ответа и длительность. Тело запроса и ответа пишется только при включённых флагах `log_request_body` / `log_response_body`. При кешировании в логах виден служебный заголовок `X-Kevinrob-Cache: HIT/MISS` (историческое имя заголовка, не означает зависимость от пакета Kevinrob).

---

## Исключения

| Исключение | Когда возникает |
|---|---|
| `ConfigurationException` | Невалидная конфигурация |
| `ValidationException` | Ошибка параметров запроса или структуры ответа |
| `AuthenticationException` | HTTP `401` / `403` |
| `RateLimitException` | Превышен rate limit |
| `ApiException` | Прочие HTTP `4xx` / `5xx` |
| `NetworkException` | Сетевые ошибки |

Все исключения пакета наследуют `DadataException`:

```php
use Ex3mm\Dadata\Exceptions\DadataException;
use Ex3mm\Dadata\Exceptions\RateLimitException;
use Ex3mm\Dadata\Exceptions\ValidationException;

try {
    $result = $client->suggestAddress()->query('Москва')->get();
} catch (RateLimitException $e) {
    // превышен лимит — повторить позже
} catch (ValidationException $e) {
    // ошибка параметров
} catch (DadataException $e) {
    // любая другая ошибка пакета
}
```

---

## Лицензия

MIT — подробности в файле [LICENSE](LICENSE).
