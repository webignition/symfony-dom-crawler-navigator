<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\Client as PantherClient;
use webignition\SymfonyPantherWebServerRunner\Options;
use webignition\SymfonyPantherWebServerRunner\WebServerRunner;

abstract class AbstractBrowserTestCase extends TestCase
{
    private const WEB_SERVER_DIR = __DIR__ . '/../fixtures/html';

    /**
     * @var WebServerRunner
     */
    private static $webServerRunner;

    /**
     * @var PantherClient
     */
    protected static $client;

    /**
     * @var string|null
     */
    protected static $webServerDir;

    /**
     * @var string|null
     */
    protected static $baseUri;

    public static function setUpBeforeClass(): void
    {
        if (null === self::$baseUri) {
            self::$baseUri = Options::getBaseUri();
        }

        self::$webServerRunner = new WebServerRunner((string) realpath(self::WEB_SERVER_DIR));
        self::$webServerRunner->start();

        self::$client = Client::createChromeClient(null, null, [], self::$baseUri);
    }

    public static function tearDownAfterClass(): void
    {
        self::$webServerRunner->stop();
        self::$client->quit();
    }
}
