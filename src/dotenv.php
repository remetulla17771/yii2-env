<?php

namespace deepn9x\env;

class dotenv
{
    private array $data = [];

    /**
     * Загружаем и парсим .env файл при создании объекта
     */
    public function __construct(?string $key = null)
    {
        $envPath = \Yii::getAlias('@app') . '/config/.env';

        if (!file_exists($envPath)) {
            file_put_contents($envPath, "test=testEnv");
        }

        $this->loadEnv($envPath);
    }

    /**
     * Чтение и парсинг .env файла
     */
    private function loadEnv(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Игнорируем пустые строки и комментарии
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Делим строку только по ПЕРВОМУ знаку "="
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $this->data[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
            }
        }
    }

    /**
     * Получить значение по ключу
     * 
     * @param string $key Ключ переменной
     * @param mixed $default Значение по умолчанию, если ключ не найден
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Синоним метода get() для удобства или старого синтаксиса
     */
    public function key(string $key, $default = null)
    {
        return $this->get($key, $default);
    }

    /**
     * Вспомогательный статический метод для быстрого вызова dotenv::env('KEY')
     */
    public static function env(string $key, $default = null)
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance->get($key, $default);
    }
}
