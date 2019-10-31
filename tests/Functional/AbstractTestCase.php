<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

abstract class AbstractTestCase extends AbstractBrowserTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::$webServerDir = __DIR__
            . '/..'
            . self::FIXTURES_RELATIVE_PATH
            . self::FIXTURES_HTML_RELATIVE_PATH;

        parent::setUpBeforeClass();
    }
}
