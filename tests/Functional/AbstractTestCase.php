<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\ProcessManager\WebServerManager;
use webignition\SymfonyDomCrawlerNavigator\Tests\Services\PantherClientFactory;
use webignition\SymfonyDomCrawlerNavigator\Tests\Services\WebServerRunner;

abstract class AbstractTestCase extends TestCase
{
    const FIXTURES_RELATIVE_PATH = '/fixtures';
    const FIXTURES_HTML_RELATIVE_PATH = '/html';

    /**
     * @var PantherClient
     */
    protected static $client;

    /**
     * @var string|null
     */
    protected static $webServerDir;

    /**
     * @var array
     */
    protected static $defaultOptions = [
        'hostname' => '127.0.0.1',
        'port' => 9080,
    ];

    /**
     * @var WebServerManager|null
     */
    protected static $webServerManager;

    /**
     * @var string|null
     */
    protected static $baseUri;

    /**
     * @var WebServerRunner
     */
    private static $webServerRunner;

    /**
     * @var PantherClientFactory
     */
    private static $pantherClientFactory;

    public static function setUpBeforeClass(): void
    {
        self::$webServerDir = (string) realpath(
            __DIR__  . '/..' . self::FIXTURES_RELATIVE_PATH . self::FIXTURES_HTML_RELATIVE_PATH
        );

        self::$webServerRunner = new WebServerRunner(self::$webServerDir);
        self::$webServerRunner->start();

        self::$baseUri = sprintf('http://%s:%s', self::$defaultOptions['hostname'], self::$defaultOptions['port']);

        self::$pantherClientFactory = new PantherClientFactory();
        self::$client = self::$pantherClientFactory->create(self::$baseUri);
    }

    public static function tearDownAfterClass(): void
    {
        static::stopWebServer();
    }

    public static function stopWebServer()
    {
        self::$webServerRunner->stop();
        self::$pantherClientFactory->destroy(self::$client);
    }
}
