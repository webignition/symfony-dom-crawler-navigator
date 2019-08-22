<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Unit;

use Facebook\WebDriver\WebDriver;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\CrawlerFactory;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;
use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;
use webignition\SymfonyDomCrawlerNavigator\Model\LocatorType;
use webignition\SymfonyDomCrawlerNavigator\Navigator;

class NavigatorTest extends \PHPUnit\Framework\TestCase
{
    public function testFindElementThrowsUnknownElementException()
    {
        $crawler = new Crawler([], \Mockery::mock(WebDriver::class));
        $elementCrawler = new Crawler([], \Mockery::mock(WebDriver::class));

        $crawlerFactory = \Mockery::mock(CrawlerFactory::class);
        $crawlerFactory
            ->shouldReceive('createElementCrawler')
            ->andReturn($elementCrawler);

        $navigator = new Navigator($crawler, $crawlerFactory);

        $elementLocator = new ElementLocator(
            LocatorType::CSS_SELECTOR,
            '.does-not-exist',
            1
        );

        try {
            $navigator->findElement($elementLocator);
            $this->fail('UnknownElementException not thrown');
        } catch (UnknownElementException $unknownElementException) {
            $this->assertSame($elementLocator, $unknownElementException->getElementLocator());
        }
    }

    public function testSetCrawler()
    {
        $crawler = new Crawler([], \Mockery::mock(WebDriver::class));
        $crawlerFactory = \Mockery::mock(CrawlerFactory::class);

        $navigator = new Navigator($crawler, $crawlerFactory);

        $reflector = new \ReflectionObject($navigator);
        $property = $reflector->getProperty('crawler');
        $property->setAccessible(true);

        $this->assertSame($property->getValue($navigator), $crawler);

        $newCrawler = new Crawler([], \Mockery::mock(WebDriver::class));
        $navigator->setCrawler($newCrawler);
        $this->assertSame($property->getValue($navigator), $newCrawler);
    }
}
