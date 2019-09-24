<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Unit\Model;

use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

class ElementLocatorTest extends \PHPUnit\Framework\TestCase
{
    public function testIsCssSelector()
    {
        $this->assertTrue((new ElementLocator('.selector'))->isCssSelector());
        $this->assertFalse((new ElementLocator('//h1'))->isCssSelector());
        $this->assertFalse((new ElementLocator(''))->isCssSelector());
    }

    public function testIsXpathExpression()
    {
        $this->assertFalse((new ElementLocator('.selector'))->isXpathExpression());
        $this->assertTrue((new ElementLocator('//h1'))->isXpathExpression());
        $this->assertFalse((new ElementLocator(''))->isXpathExpression());
    }
}
