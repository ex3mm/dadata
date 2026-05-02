# Changelog

Все значимые изменения в этом проекте будут документированы в этом файле.

Формат основан на [Keep a Changelog](https://keepachangelog.com/ru/1.0.0/),
и этот проект придерживается [Semantic Versioning](https://semver.org/lang/ru/).

## [Unreleased]

## [2.1.0] - 2026-05-02

### Добавлено
- Поддержка полей `citizenship` и `fio` для индивидуальных предпринимателей в `PartyDataDto`
  - `CitizenshipDto` — гражданство ИП с кодом страны и названием
  - `CitizenshipCodeDto` — числовой и буквенный код страны (numeric, alpha_3)
  - `CitizenshipNameDto` — полное и краткое название страны
  - `FioDto` — ФИО индивидуального предпринимателя (surname, name, patronymic, gender, source, qc)
- Поле `invalid` для юридических лиц в `PartyDataDto` — признак недействительности
- Поля `sites` и `financeHistory` в `PartyDataDto` для сайтов организации и истории финансов
- Endpoint для получения подсказок по ФИО (`/suggest/fio`)
  - `SuggestFioEndpoint` — эндпоинт для подсказок по ФИО
  - `SuggestFioRequest` — request builder с методами `query()`, `count()`, `parts()`, `gender()`
  - `FioSuggestionResponseDto` — DTO подсказки ФИО
  - `FioDataDto` — DTO данных ФИО из подсказок
  - `Gender` enum — пол (MALE, FEMALE, UNKNOWN)
- Метод `suggestFio()` в `DadataClient` и `DadataClientInterface`

### Изменено
- Переупорядочены поля в `PartyDataDto` в соответствии с порядком полей в API DaData:
  - Поля для ИП (citizenship, fio) — в начале
  - Общие поля — в порядке как в API ответе
  - Поля для юрлиц (kpp, capital, management и т.д.) — в конце

### Тесты
- Добавлены тесты для индивидуальных предпринимателей (`PartyIndividualEntrepreneurTest`)
  - Тест активного ИП с citizenship и fio
  - Тест ликвидированного ИП
  - Тест обработки отсутствующих полей для юрлиц
  - Тест сериализации в массив
- Добавлены тесты для юридических лиц (`PartyLegalEntityTest`)
  - Тест юрлица со всеми полями (kpp, capital, invalid, management, founders, managers)
  - Тест сериализации в массив
- Добавлены тесты для подсказок ФИО (`SuggestFioResponseTest`)
  - Тест парсинга мужского имени
  - Тест парсинга женского имени
  - Тест парсинга фамилии
  - Тест парсинга с неопределённым полом
  - Тест сериализации в массив

## [2.0.0] - 2026-04-05

### Изменено
- Проведен крупный рефакторинг публичного API пакета и контрактов ответа.
- Унифицированы Request Builder методы и формат возвращаемых коллекций через `CollectionResponse<T>`.
- Приведены к единому стилю DTO, enum и структура shared-моделей между endpoint.
- Расширен и стабилизирован набор поддерживаемых endpoint Suggestions/Cleaner API, включая поиск организаций и банков, подсказки и стандартизацию адреса.
- Улучшен механизм произвольных запросов (`custom`/`getCustom`) с возвратом оригинального body DaData.
- Расширены упрощенные методы Facade для типовых сценариев.
- Усилена валидация входных параметров Request Builder-ов.
- Существенно обновлена документация:
  - единый формат описания endpoint и методов;
  - единообразное описание enum-параметров;
  - расширенные примеры standalone/Laravel/Facade.
- Обновлены и расширены тесты (unit + интеграционные фикстуры) в соответствии с новым API.

## [1.0.0] - 2026-03-12

### Добавлено
- Типобезопасная интеграция с DaData.ru API (PHP 8.5 + strict types)
- Поддержка Laravel 12 и standalone PHP
- 5 endpoints: Clean Address, Suggest Address, Suggest Party, Find Party, Raw
- Fluent Request Builders для удобного построения запросов
- readonly DTO с доступом к оригинальному JSON-ответу
- Автоматическое кеширование успешных ответов (2xx) с поддержкой PSR-16
- Rate limiting с алгоритмом Sliding Window
- Retry с exponential backoff
- Полная маскировка API-ключей в логах через `MaskingLogger`
- Lazy initialization HTTP-клиента
- PSR-3 (логирование) и PSR-16 (кеширование) совместимость
- Middleware stack: Logging, Retry, Cache, RateLimiter
- Специализированные исключения для различных типов ошибок:
  - `ApiException` для HTTP 4xx/5xx от API
  - `NetworkException` для сетевых ошибок (timeout, connection refused)
  - `AuthenticationException` для ошибок авторизации (401, 403)
  - `RateLimitException` для превышения лимита запросов (429)
  - `ConfigurationException` для невалидной конфигурации
  - `ValidationException` для ошибок валидации данных
- Laravel auto-discovery для автоматической регистрации Service Provider
- Standalone режим через `DadataFactory` для использования без Laravel
- Типизированные Enum для параметров запросов:
  - `AddressBound` для уровней детализации адреса
  - `PartyType` для типов организаций
  - `PartyStatus` для статусов организаций
  - `Language` для выбора языка подсказок
  - `AddressFiasLevel` для уровней классификатора ФИАС
- Полная документация API методов и Enum значений в README.md

[1.0.0]: https://github.com/ex3mm/dadata/releases/tag/v1.0.0
[2.0.0]: https://github.com/ex3mm/dadata/releases/tag/v2.0.0
[2.1.0]: https://github.com/ex3mm/dadata/releases/tag/v2.1.0
