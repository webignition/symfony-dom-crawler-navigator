<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

abstract class TestCase extends PantherTestCase
{
    const FIXTURES_RELATIVE_PATH = '/fixtures';
    const FIXTURES_HTML_RELATIVE_PATH = '/html';

    /**
     * @var Client
     */
    protected static $client;

    protected function setUp(): void
    {
        self::$webServerDir = realpath(
            __DIR__  . '/..' . self::FIXTURES_RELATIVE_PATH . self::FIXTURES_HTML_RELATIVE_PATH
        );

        self::$client = self::createPantherClient();
    }
}
