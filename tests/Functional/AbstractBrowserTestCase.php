<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

abstract class AbstractBrowserTestCase extends \webignition\BasePantherTestCase\AbstractBrowserTestCase
{
    private const WEB_SERVER_DIR = __DIR__ . '/../fixtures/html';

    public static function setUpBeforeClass(): void
    {
        self::$webServerDir = self::WEB_SERVER_DIR;

        parent::setUpBeforeClass();
    }
}
