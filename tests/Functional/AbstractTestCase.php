<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\ProcessManager\WebServerManager;

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

    public static function setUpBeforeClass(): void
    {
        self::$webServerDir = (string) realpath(
            __DIR__  . '/..' . self::FIXTURES_RELATIVE_PATH . self::FIXTURES_HTML_RELATIVE_PATH
        );

        self::startWebServer();
        self::$client = PantherClient::createChromeClient(null, null, [], self::$baseUri);
    }

    public static function tearDownAfterClass(): void
    {
        static::stopWebServer();
    }

    public static function startWebServer(): void
    {
        if (null !== static::$webServerManager) {
            return;
        }

        self::$webServerManager = new WebServerManager(
            (string) static::$webServerDir,
            self::$defaultOptions['hostname'],
            self::$defaultOptions['port']
        );
        self::$webServerManager->start();

        self::$baseUri = sprintf('http://%s:%s', self::$defaultOptions['hostname'], self::$defaultOptions['port']);
    }


    public static function stopWebServer()
    {
        if (null !== self::$webServerManager) {
            self::$webServerManager->quit();
            self::$webServerManager = null;
        }

        if (null !== self::$client) {
            self::$client->quit(false);
            self::$client->getBrowserManager()->quit();
        }
    }
}
