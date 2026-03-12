# ex3mm/dadata

Production-ready Laravel-пакет для интеграции с DaData.ru API.

## Возможности

- Типобезопасная работа с DaData API (PHP 8.5 + strict types)
- Поддержка Laravel 12 и standalone PHP
- Кеширование успешных ответов
- Rate limiting с алгоритмом Sliding Window
- Retry с exponential backoff
- Полная маскировка API-ключей в логах
- Lazy initialization HTTP-клиента
- PSR-3 (логирование)
- PSR-16 (кеширование)

## Требования

- PHP ^8.5
- Laravel ^12.0 (опционально)

## Установка

### Laravel

```bash
composer require ex3mm/dadata
```

Опубликуйте конфигурацию:

```bash
php artisan vendor:publish --tag=dadata-config
```

Добавьте в `.env`:

```env
DADATA_API_KEY=your_api_key_here
DADATA_SECRET_KEY=your_secret_key_here
```

### Standalone PHP

```bash
composer require ex3mm/dadata
```

## Быстрый старт

### Laravel

```php
use Ex3mm\Dadata\Laravel\Facades\Dadata;

// Подсказки по адресам
$response = Dadata::suggestAddress()
    ->query('Москва, Тверская')
    ->count(5)
    ->send();

foreach ($response->suggestions as $suggestion) {
    echo $suggestion->value . PHP_EOL;
}
```

### Standalone

```php
use Ex3mm\Dadata\DadataFactory;

$client = DadataFactory::create(
    apiKey: 'your_api_key',
    secretKey: 'your_secret_key'
);

$response = $client->suggestAddress()
    ->query('Москва, Тверская')
    ->send();
```

## Конфигурация

Все параметры настраиваются через `config/dadata.php` или переменные окружения:

```env
# Credentials
DADATA_API_KEY=
DADATA_SECRET_KEY=

# HTTP
DADATA_CONNECT_TIMEOUT=10
DADATA_TIMEOUT=30

# Retry
DADATA_RETRY_ATTEMPTS=3
DADATA_RETRY_DELAY=100

# Cache
DADATA_CACHE_ENABLED=true
DADATA_CACHE_TTL=3600
DADATA_CACHE_STORE=

# Logging
DADATA_LOG_LEVEL=info
DADATA_LOG_REQUEST_BODY=false
DADATA_LOG_RESPONSE_BODY=false

# Rate Limiting
DADATA_RATE_LIMIT_ENABLED=true
DADATA_RATE_LIMIT=20
```

### ⚠️ Важно: Кеширование в production

По умолчанию пакет использует `InMemoryCache`, который **не подходит для production** из-за:
- Потери данных при перезапуске приложения
- Race condition при параллельных запросах
- Отсутствия синхронизации между процессами

**Для production рекомендуется использовать Redis:**

```env
# .env
DADATA_CACHE_STORE=redis
```

```php
// config/dadata.php
'cache_store' => env('DADATA_CACHE_STORE', 'redis'),
```

Убедитесь, что Redis настроен в `config/cache.php`:

```php
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
    ],
],
```

## API Endpoints

### 1. Стандартизация адресов (Clean Address)

```php
$response = Dadata::cleanAddress()
    ->address('мск сухонская 11 89')
    ->send();

echo $response->result; // Москва, ул Сухонская, д 11, кв 89
```

### 2. Подсказки по адресам (Suggest Address)

```php
$response = Dadata::suggestAddress()
    ->query('Москва, Тверская')
    ->count(10)
    ->fromBound(AddressBound::STREET)
    ->toBound(AddressBound::HOUSE)
    ->send();
```

### 3. Подсказки по организациям (Suggest Party)

```php
$response = Dadata::suggestParty()
    ->query('Сбербанк')
    ->count(5)
    ->status(PartyStatus::ACTIVE)
    ->send();
```

### 4. Поиск организации по ИНН/ОГРН (Find Party)

```php
$response = Dadata::findParty()
    ->query('7707083893') // ИНН
    ->send();
```

### 5. Произвольный запрос (Raw)

```php
$response = Dadata::raw()
    ->url('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/bank')
    ->method('POST')
    ->body(['query' => 'Сбербанк'])
    ->send();
```

## Детальное описание API методов

### SuggestAddressRequest - Подсказки по адресам

Fluent builder для получения подсказок адресов от DaData API.

#### Методы

**query(string $query): static**
- Устанавливает поисковый запрос
- Обязательный параметр
- Пример: `->query('Москва, Тверская')`

**count(int $count): static**
- Устанавливает количество подсказок (1-20)
- По умолчанию: 10
- Пример: `->count(5)`

**fromBound(AddressBound $bound): static**
- Устанавливает нижнюю границу детализации адреса
- Опциональный параметр
- Пример: `->fromBound(AddressBound::STREET)`

**toBound(AddressBound $bound): static**
- Устанавливает верхнюю границу детализации адреса
- Опциональный параметр
- Пример: `->toBound(AddressBound::HOUSE)`

**language(Language $language): static**
- Устанавливает язык подсказок
- По умолчанию: Language::RU
- Пример: `->language(Language::EN)`

**locations(array $locations): static**
- Устанавливает географические ограничения для поиска
- Опциональный параметр
- Пример: `->locations(['region' => 'Москва'])`

**send(): SuggestAddressResponse**
- Отправляет запрос и возвращает типизированный ответ
- Бросает исключения при ошибках

#### Пример использования

```php
use Ex3mm\Dadata\DTO\Enums\AddressBound;
use Ex3mm\Dadata\DTO\Enums\Language;
use Ex3mm\Dadata\Laravel\Facades\Dadata;

$response = Dadata::suggestAddress()
    ->query('Москва, Тверская')
    ->count(10)
    ->fromBound(AddressBound::STREET)
    ->toBound(AddressBound::HOUSE)
    ->language(Language::RU)
    ->send();

foreach ($response->suggestions as $suggestion) {
    echo $suggestion->value . PHP_EOL;
    echo $suggestion->data->city . PHP_EOL;
}
```

---

### SuggestPartyRequest - Подсказки по организациям

Fluent builder для получения подсказок организаций от DaData API.

#### Методы

**query(string $query): static**
- Устанавливает поисковый запрос (название, ИНН, ОГРН)
- Обязательный параметр
- Пример: `->query('Сбербанк')`

**count(int $count): static**
- Устанавливает количество подсказок
- По умолчанию: 10
- Пример: `->count(5)`

**status(PartyStatus $status): static**
- Фильтрует по статусу организации
- Опциональный параметр
- Пример: `->status(PartyStatus::ACTIVE)`

**type(PartyType $type): static**
- Фильтрует по типу организации
- Опциональный параметр
- Пример: `->type(PartyType::LEGAL)`

**send(): SuggestPartyResponse**
- Отправляет запрос и возвращает типизированный ответ
- Бросает исключения при ошибках

#### Пример использования

```php
use Ex3mm\Dadata\DTO\Enums\PartyStatus;
use Ex3mm\Dadata\DTO\Enums\PartyType;
use Ex3mm\Dadata\Laravel\Facades\Dadata;

$response = Dadata::suggestParty()
    ->query('Сбербанк')
    ->count(5)
    ->status(PartyStatus::ACTIVE)
    ->type(PartyType::LEGAL)
    ->send();

foreach ($response->suggestions as $suggestion) {
    echo $suggestion->value . PHP_EOL;
    echo 'ИНН: ' . $suggestion->data->inn . PHP_EOL;
}
```

---

### CleanAddressRequest - Стандартизация адресов

Fluent builder для стандартизации и очистки адресов.

#### Методы

**address(string $address): static**
- Устанавливает адрес для стандартизации
- Обязательный параметр
- Пример: `->address('мск сухонская 11 89')`

**send(): CleanAddressResponse**
- Отправляет запрос и возвращает типизированный ответ
- Бросает исключения при ошибках

#### Пример использования

```php
use Ex3mm\Dadata\Laravel\Facades\Dadata;

$response = Dadata::cleanAddress()
    ->address('мск сухонская 11 89')
    ->send();

echo $response->result; // Москва, ул Сухонская, д 11, кв 89
echo $response->postalCode; // 127642
echo $response->fiasId; // UUID адреса в ФИАС
```

---

### FindPartyRequest - Поиск организации по ИНН/ОГРН

Fluent builder для точного поиска организаций по идентификаторам.

#### Методы

**query(string $innOrOgrn): static**
- Устанавливает ИНН (10 или 12 цифр) или ОГРН (13 или 15 цифр)
- Обязательный параметр
- Валидирует формат идентификатора
- Пример: `->query('7707083893')`

**count(int $count): static**
- Устанавливает количество результатов
- По умолчанию: 10
- Пример: `->count(1)`

**type(PartyType $type): static**
- Фильтрует по типу организации
- Опциональный параметр
- Пример: `->type(PartyType::LEGAL)`

**send(): FindPartyResponse**
- Отправляет запрос и возвращает типизированный ответ
- Бросает исключения при ошибках

#### Пример использования

```php
use Ex3mm\Dadata\DTO\Enums\PartyType;
use Ex3mm\Dadata\Laravel\Facades\Dadata;

$response = Dadata::findParty()
    ->query('7707083893') // ИНН Сбербанка
    ->count(1)
    ->type(PartyType::LEGAL)
    ->send();

if (!empty($response->suggestions)) {
    $party = $response->suggestions[0];
    echo $party->value . PHP_EOL;
    echo 'ОГРН: ' . $party->data->ogrn . PHP_EOL;
    echo 'Адрес: ' . $party->data->address->value . PHP_EOL;
}
```

---

### RawRequest - Произвольный запрос

Fluent builder для выполнения произвольных запросов к DaData API.

#### Методы

**url(string $url): static**
- Устанавливает полный URL для запроса
- Обязательный параметр
- Пример: `->url('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/bank')`

**method(string $method): static**
- Устанавливает HTTP-метод
- По умолчанию: POST
- Пример: `->method('POST')`

**body(array $body): static**
- Устанавливает тело запроса
- Опциональный параметр
- Пример: `->body(['query' => 'Сбербанк'])`

**withHeaders(array $headers): static**
- Добавляет дополнительные заголовки
- Опциональный параметр
- Пример: `->withHeaders(['X-Custom-Header' => 'value'])`

**send(): RawResponse**
- Отправляет запрос и возвращает сырой ответ
- Бросает исключения при ошибках

#### Пример использования

```php
use Ex3mm\Dadata\Laravel\Facades\Dadata;

$response = Dadata::raw()
    ->url('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/bank')
    ->method('POST')
    ->body(['query' => 'Сбербанк', 'count' => 5])
    ->send();

$data = $response->data; // Массив с результатами
```

## Enum значения

Пакет предоставляет типизированные Enum для параметров запросов.

### AddressBound - Уровни детализации адреса

Используется для ограничения детализации подсказок адресов через методы `fromBound()` и `toBound()`.

| Значение | Описание | Пример использования |
|----------|----------|---------------------|
| `AddressBound::COUNTRY` | Страна | `->fromBound(AddressBound::COUNTRY)` |
| `AddressBound::REGION` | Регион | `->fromBound(AddressBound::REGION)` |
| `AddressBound::AREA` | Район | `->fromBound(AddressBound::AREA)` |
| `AddressBound::CITY` | Город | `->fromBound(AddressBound::CITY)` |
| `AddressBound::CITY_DISTRICT` | Район города | `->fromBound(AddressBound::CITY_DISTRICT)` |
| `AddressBound::SETTLEMENT` | Населённый пункт | `->fromBound(AddressBound::SETTLEMENT)` |
| `AddressBound::STREET` | Улица | `->fromBound(AddressBound::STREET)` |
| `AddressBound::HOUSE` | Дом | `->toBound(AddressBound::HOUSE)` |
| `AddressBound::FLAT` | Квартира | `->toBound(AddressBound::FLAT)` |

#### Примеры

```php
// Только улицы и дома (без квартир)
$response = Dadata::suggestAddress()
    ->query('Москва, Тверская')
    ->fromBound(AddressBound::STREET)
    ->toBound(AddressBound::HOUSE)
    ->send();

// Только города (без улиц)
$response = Dadata::suggestAddress()
    ->query('Москва')
    ->fromBound(AddressBound::CITY)
    ->toBound(AddressBound::CITY)
    ->send();

// От региона до улицы
$response = Dadata::suggestAddress()
    ->query('Московская область')
    ->fromBound(AddressBound::REGION)
    ->toBound(AddressBound::STREET)
    ->send();
```

---

### PartyType - Тип организации

Используется для фильтрации организаций по типу в методах `suggestParty()` и `findParty()`.

| Значение | Описание | Пример использования |
|----------|----------|---------------------|
| `PartyType::LEGAL` | Юридическое лицо (ООО, АО, ПАО и т.д.) | `->type(PartyType::LEGAL)` |
| `PartyType::INDIVIDUAL` | Индивидуальный предприниматель (ИП) | `->type(PartyType::INDIVIDUAL)` |

#### Примеры

```php
// Только юридические лица
$response = Dadata::suggestParty()
    ->query('Сбербанк')
    ->type(PartyType::LEGAL)
    ->send();

// Только ИП
$response = Dadata::suggestParty()
    ->query('Иванов')
    ->type(PartyType::INDIVIDUAL)
    ->send();
```

---

### PartyStatus - Статус организации

Используется для фильтрации организаций по статусу в методе `suggestParty()`.

| Значение | Описание | Пример использования |
|----------|----------|---------------------|
| `PartyStatus::ACTIVE` | Действующая организация | `->status(PartyStatus::ACTIVE)` |
| `PartyStatus::LIQUIDATING` | Организация в процессе ликвидации | `->status(PartyStatus::LIQUIDATING)` |
| `PartyStatus::LIQUIDATED` | Ликвидированная организация | `->status(PartyStatus::LIQUIDATED)` |
| `PartyStatus::BANKRUPT` | Организация в процессе банкротства | `->status(PartyStatus::BANKRUPT)` |
| `PartyStatus::REORGANIZING` | Организация в процессе реорганизации | `->status(PartyStatus::REORGANIZING)` |

#### Примеры

```php
// Только действующие организации
$response = Dadata::suggestParty()
    ->query('Сбербанк')
    ->status(PartyStatus::ACTIVE)
    ->send();

// Ликвидированные организации
$response = Dadata::suggestParty()
    ->query('Старая компания')
    ->status(PartyStatus::LIQUIDATED)
    ->send();

// Комбинация фильтров
$response = Dadata::suggestParty()
    ->query('ООО')
    ->type(PartyType::LEGAL)
    ->status(PartyStatus::ACTIVE)
    ->send();
```

---

### Language - Язык подсказок

Используется для выбора языка подсказок адресов в методе `suggestAddress()`.

| Значение | Описание | Пример использования |
|----------|----------|---------------------|
| `Language::RU` | Русский язык (по умолчанию) | `->language(Language::RU)` |
| `Language::EN` | Английский язык | `->language(Language::EN)` |

#### Примеры

```php
// Подсказки на русском (по умолчанию)
$response = Dadata::suggestAddress()
    ->query('Москва')
    ->language(Language::RU)
    ->send();
// Результат: "г Москва"

// Подсказки на английском
$response = Dadata::suggestAddress()
    ->query('Moscow')
    ->language(Language::EN)
    ->send();
// Результат: "Moscow"
```

---

### AddressFiasLevel - Уровень ФИАС

Enum для работы с уровнями классификатора ФИАС. Используется в DTO ответов для определения уровня детализации адреса.

| Значение | Код | Описание |
|----------|-----|----------|
| `AddressFiasLevel::UNKNOWN` | -1 | Неизвестный уровень |
| `AddressFiasLevel::COUNTRY` | 0 | Страна |
| `AddressFiasLevel::REGION` | 1 | Регион |
| `AddressFiasLevel::AREA` | 3 | Район |
| `AddressFiasLevel::CITY` | 4 | Город |
| `AddressFiasLevel::CITY_DISTRICT` | 5 | Район города |
| `AddressFiasLevel::SETTLEMENT` | 6 | Населённый пункт |
| `AddressFiasLevel::STREET` | 7 | Улица |
| `AddressFiasLevel::HOUSE` | 8 | Дом |
| `AddressFiasLevel::FLAT` | 9 | Квартира |

#### Пример использования

```php
use Ex3mm\Dadata\DTO\Enums\AddressFiasLevel;
use Ex3mm\Dadata\Laravel\Facades\Dadata;

$response = Dadata::suggestAddress()
    ->query('Москва, Тверская, 1')
    ->send();

foreach ($response->suggestions as $suggestion) {
    $fiasLevel = $suggestion->data->fiasLevel;
    
    if ($fiasLevel === AddressFiasLevel::HOUSE) {
        echo "Адрес детализирован до дома: {$suggestion->value}" . PHP_EOL;
    } elseif ($fiasLevel === AddressFiasLevel::STREET) {
        echo "Адрес детализирован до улицы: {$suggestion->value}" . PHP_EOL;
    }
}
```

## Обработка исключений

Пакет предоставляет специализированные исключения для различных типов ошибок:

### Иерархия исключений

```
DadataException (базовое исключение)
├── ApiException (HTTP 4xx/5xx от API)
├── NetworkException (сетевые ошибки: timeout, connection refused)
├── AuthenticationException (401/403)
├── RateLimitException (429, превышение лимита)
└── ConfigurationException (невалидная конфигурация)
```

### Примеры обработки

#### ApiException — ошибки API

```php
use Ex3mm\Dadata\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;

try {
    $response = Dadata::cleanAddress()
        ->address('невалидный адрес')
        ->send();
} catch (ApiException $e) {
    Log::error('Ошибка DaData API', [
        'status' => $e->getStatusCode(),
        'message' => $e->getMessage(),
        'raw_response' => $e->getRawResponse(),
    ]);
    
    // Обработка в зависимости от статуса
    if ($e->getStatusCode() === 400) {
        return response()->json(['error' => 'Невалидный адрес'], 400);
    }
}
```

#### RateLimitException — превышение лимита запросов

```php
use Ex3mm\Dadata\Exceptions\RateLimitException;

try {
    $response = Dadata::suggestAddress()
        ->query('Москва')
        ->send();
} catch (RateLimitException $e) {
    // Получаем время до следующей попытки
    $retryAfter = $e->getRetryAfter();
    
    Log::warning("Rate limit exceeded, retry after {$retryAfter}s");
    
    // Ждём и повторяем
    sleep($retryAfter);
    $response = Dadata::suggestAddress()->query('Москва')->send();
}
```

#### NetworkException — сетевые ошибки

```php
use Ex3mm\Dadata\Exceptions\NetworkException;

try {
    $response = Dadata::findParty()
        ->query('7707083893')
        ->send();
} catch (NetworkException $e) {
    Log::error('Сетевая ошибка DaData', [
        'message' => $e->getMessage(),
        'previous' => $e->getPrevious()?->getMessage(),
    ]);
    
    // Fallback или повторная попытка
    return $this->fallbackPartySearch($inn);
}
```

#### AuthenticationException — ошибки авторизации

```php
use Ex3mm\Dadata\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\Notification;

try {
    $response = Dadata::suggestParty()
        ->query('Сбербанк')
        ->send();
} catch (AuthenticationException $e) {
    // Критическая ошибка — уведомляем администратора
    Log::critical('Ошибка авторизации DaData', [
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
    ]);
    
    Notification::send($admin, new DadataAuthFailedNotification($e));
    
    throw $e; // Пробрасываем дальше
}
```

#### Обработка всех исключений

```php
use Ex3mm\Dadata\Exceptions\DadataException;

try {
    $response = Dadata::cleanAddress()
        ->address($address)
        ->send();
} catch (DadataException $e) {
    // Обрабатываем любое исключение пакета
    Log::error('DaData error', [
        'type' => get_class($e),
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
    ]);
    
    return null; // Или fallback-значение
}
```

## Безопасность

API-ключи автоматически маскируются во всех логах и исключениях:

```php
// В логах вместо реального ключа будет "***"
[2026-03-11 10:00:00] info: POST https://suggestions.dadata.ru/...?key=***
```
