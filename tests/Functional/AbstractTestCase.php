<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client as PantherClient;
use webignition\SymfonyDomCrawlerNavigator\Tests\Services\Options;
use webignition\SymfonyDomCrawlerNavigator\Tests\Services\PantherClientFactory;
use webignition\SymfonyDomCrawlerNavigator\Tests\Services\WebServerRunner;

abstract class AbstractTestCase extends TestCase
{
    const FIXTURES_RELATIVE_PATH = '/fixtures';
    const FIXTURES_HTML_RELATIVE_PATH = '/html';

    /**
     * @var WebServerRunner
     */
    private static $webServerRunner;

    /**
     * @var PantherClientFactory
     */
    private static $pantherClientFactory;

    /**
     * @var PantherClient
     */
    protected static $client;

    public static function setUpBeforeClass(): void
    {
        $webServerDir = (string) realpath(
            __DIR__  . '/..' . self::FIXTURES_RELATIVE_PATH . self::FIXTURES_HTML_RELATIVE_PATH
        );

        self::$webServerRunner = new WebServerRunner($webServerDir);
        self::$webServerRunner->start();

        self::$pantherClientFactory = new PantherClientFactory();
        self::$client = self::$pantherClientFactory->create(Options::getBaseUri());
    }

    public static function tearDownAfterClass(): void
    {
        static::stopWebServer();
    }

    private static function stopWebServer()
    {
        self::$webServerRunner->stop();
        self::$pantherClientFactory->destroyClient(self::$client);
    }
}
