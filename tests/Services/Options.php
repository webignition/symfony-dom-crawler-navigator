<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Services;

class Options
{
    const DEFAULT_HOSTNAME = '127.0.0.1';
    const DEFAULT_PORT = 9080;

    private static $default = [
        'hostname' => self::DEFAULT_HOSTNAME,
        'port' => self::DEFAULT_PORT,
    ];

    private static $options = null;

    public static function getDefault(): array
    {
        return self::$default;
    }

    public static function get(): array
    {
        if (null === self::$options) {
            self::$options = self::$default;
        }

        return self::$options;
    }

    public static function set(array $options): void
    {
        self::$options = array_merge(self::$options, $options);
    }

    public static function getBaseUri(): string
    {
        $options = self::get();

        return sprintf('http://%s:%s', $options['hostname'], $options['port']);
    }
}
