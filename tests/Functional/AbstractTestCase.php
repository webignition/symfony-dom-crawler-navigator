<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use PHPUnit\Framework\TestCase;
use webignition\SymfonyDomCrawlerNavigator\Tests\Services\Options;
use webignition\SymfonyDomCrawlerNavigator\Tests\Services\PantherClientContainer;
use webignition\SymfonyDomCrawlerNavigator\Tests\Services\WebServerRunner;

abstract class AbstractTestCase extends TestCase
{
    const FIXTURES_RELATIVE_PATH = '/fixtures';
    const FIXTURES_HTML_RELATIVE_PATH = '/html';

    /**
     * @var string|null
     */
    protected static $webServerDir;

    /**
     * @var WebServerRunner
     */
    private static $webServerRunner;

    /**
     * @var PantherClientContainer
     */
    protected static $pantherClientContainer;

    public static function setUpBeforeClass(): void
    {
        self::$webServerDir = (string) realpath(
            __DIR__  . '/..' . self::FIXTURES_RELATIVE_PATH . self::FIXTURES_HTML_RELATIVE_PATH
        );

        self::$webServerRunner = new WebServerRunner(self::$webServerDir);
        self::$webServerRunner->start();

        self::$pantherClientContainer = new PantherClientContainer(Options::getBaseUri());
    }

    public static function tearDownAfterClass(): void
    {
        static::stopWebServer();
    }

    public static function stopWebServer()
    {
        self::$webServerRunner->stop();
        self::$pantherClientContainer->destroy();
    }
}
