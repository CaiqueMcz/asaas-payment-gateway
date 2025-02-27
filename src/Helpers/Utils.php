<?php

namespace CaiqueMcz\AsaasPaymentGateway\Helpers;

class Utils
{
    /**
     * @throws \Exception
     */
    public static function generateRandomFloat($min = 5000, $max = 50000): float
    {
        $number = random_int($min, $max) / 100;
        return (float)$number;
    }

    public static function getPaymentsJsonFile($file, $replaces = []): ?array
    {
        return self::getJsonFile('tests/Mocks/payments/' . $file, $replaces);
    }

    /**
     * @throws \Exception
     */
    public static function getJsonFile($file, $replaces = []): ?array
    {
        $file = self::parseDir($file);
        $content = file_get_contents($file);
        foreach ($replaces as $key => $value) {
            $content = str_replace('${' . $key . '}', $value, $content);
        }
        return json_decode($content, true);
    }

    public static function parseDir($dir): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $dir);
    }

    /**
     * @throws \Exception
     */
    public static function getInstallmentsJsonFile($file, $replaces = []): ?array
    {
        return self::getJsonFile('tests/Mocks/installments/' . $file, $replaces);
    }

    /**
     * @throws \Exception
     */
    public static function getSubscriptionsJsonFile($file, $replaces = []): ?array
    {
        return self::getJsonFile('tests/Mocks/subscriptions/' . $file, $replaces);
    }

    /**
     * @throws \Exception
     */
    public static function getSplitsJsonFile($file, $replaces = []): ?array
    {
        return self::getJsonFile('tests/Mocks/splits/' . $file, $replaces);
    }

    public static function getDueDate(int $qtyDays = 30): string
    {
        return date('Y-m-d', strtotime("+$qtyDays days"));
    }

    public static function generateGetterSetterArray(string $attribute): array
    {
        if (self::strContains('|bool', $attribute)) {
            $explode = explode('|bool', $attribute);
            $attribute = $explode[0];
            $camelCase = ucfirst($attribute);
            return [
                'get' => 'is' . $camelCase,
                'set' => 'setIs' . $camelCase,
                'attribute' => $attribute
            ];
        }
        $camelCase = ucfirst($attribute);

        return [
            'get' => 'get' . $camelCase,
            'set' => 'set' . $camelCase,
            'attribute' => $attribute
        ];
    }

    public static function strContains($needle, $haystack): bool
    {
        return strpos($haystack, $needle) !== false;
    }

    public static function createJsonFile(string $name, string $contents)
    {
        $name = str_replace("/", ":", $name) . '.json';
        file_put_contents(self::baseDir('files/' . $name), $contents);
    }

    public static function baseDir(string $file = ""): string
    {
        return self::parseDir(__DIR__ . '/../../' . $file);
    }
}
