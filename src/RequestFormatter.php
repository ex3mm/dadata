<?php

declare(strict_types=1);

namespace Ex3mm\Dadata;

use Ex3mm\Dadata\Requests\AbstractRequest;

/**
 * Форматирует запросы для логирования с маскировкой API ключей.
 *
 * @codeCoverageIgnore Класс не используется в текущей реализации
 */
final class RequestFormatter
{
    /**
     * Форматирует запрос в JSON строку.
     *
     *
     * @return non-empty-string
     */
    public function format(AbstractRequest $request): string
    {
        $data = $this->extractRequestData($request);

        return json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Форматирует запрос для логирования с маскировкой API ключей.
     *
     *
     * @return non-empty-string
     */
    public function formatForLog(AbstractRequest $request): string
    {
        $data = $this->extractRequestData($request);
        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        // Маскируем API ключи в JSON
        $json = $this->maskSensitiveData($json);

        return $json;
    }

    /**
     * Извлекает данные из запроса через рефлексию.
     *
     *
     * @return array<string, mixed>
     */
    private function extractRequestData(AbstractRequest $request): array
    {
        $reflection = new \ReflectionClass($request);
        $data       = [];

        foreach ($reflection->getProperties() as $property) {
            $value = $property->getValue($request);

            if ($value !== null) {
                $data[$property->getName()] = $value;
            }
        }

        return $data;
    }

    /**
     * Маскирует чувствительные данные в JSON строке.
     *
     *
     * @return non-empty-string
     */
    private function maskSensitiveData(string $json): string
    {
        // Паттерны для поиска API ключей
        $patterns = [
            '/(["\']?(?:api_?key|apiKey)["\']?\s*[:=]\s*["\'])([^"\']+)(["\'])/'       => '$1***$3',
            '/(["\']?(?:secret_?key|secretKey)["\']?\s*[:=]\s*["\'])([^"\']+)(["\'])/' => '$1***$3',
            '/(Token\s+)([a-zA-Z0-9]+)/'                                               => '$1***',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $result = preg_replace($pattern, $replacement, $json);
            if ($result !== null) {
                $json = $result;
            }
        }

        return $json !== '' ? $json : '{}';
    }
}
