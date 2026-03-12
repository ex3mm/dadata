# Changelog

Все значимые изменения в этом проекте будут документированы в этом файле.

Формат основан на [Keep a Changelog](https://keepachangelog.com/ru/1.0.0/),
и этот проект придерживается [Semantic Versioning](https://semver.org/lang/ru/).

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
