<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\CrawlerFactory;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;
use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;
use webignition\SymfonyDomCrawlerNavigator\Model\LocatorType;

class CrawlerFactoryTest extends AbstractTestCase
{
    /**
     * @dataProvider createElementCrawlerSuccessDataProvider
     */
    public function testCreateElementCrawlerSuccess(
        ElementLocator $elementLocator,
        ?ElementLocator $scope,
        callable $assertions
    ) {
        $crawler = self::$client->request('GET', '/basic.html');
        $crawlerFactory = CrawlerFactory::create($crawler);

        $elementCrawler = $crawlerFactory->createElementCrawler($elementLocator, $crawler);
        $this->assertCount(1, $elementCrawler);

        $assertions($elementCrawler);
    }

    public function createElementCrawlerSuccessDataProvider(): array
    {
        return [
            'first h1 with css selector' => [
                'elementLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'h1',
                    1
                ),
                'scope' => null,
                'assertions' => function (Crawler $crawler) {
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
            'first h1 with xpath expression' => [
                'elementLocator' => new ElementLocator(
                    LocatorType::XPATH_EXPRESSION,
                    '//h1',
                    1
                ),
                'scope' => null,
                'assertions' => function (Crawler $crawler) {
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
            'second h1 with css selector' => [
                'elementLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'h1',
                    2
                ),
                'scope' => null,
                'assertions' => function (Crawler $crawler) {
                    $this->assertSame('Main', $crawler->getText());
                },
            ],
        ];
    }

    public function testCreateElementCrawlerThrowsUnknownElementException()
    {
        $crawler = self::$client->request('GET', '/basic.html');
        $crawlerFactory = CrawlerFactory::create($crawler);

        $elementLocator = new ElementLocator(
            LocatorType::CSS_SELECTOR,
            '.does-not-exist',
            1
        );

        try {
            $crawlerFactory->createElementCrawler($elementLocator, $crawler);
            $this->fail('UnknownElementException not thrown');
        } catch (UnknownElementException $unknownElementException) {
            $this->assertSame($elementLocator, $unknownElementException->getElementLocator());
        }
    }

    /**
     * @dataProvider findElementThrowsInvalidPositionExceptionDataProvider
     */
    public function testCreateElementCrawlerThrowsInvalidElementPositionException(
        string $cssLocator,
        int $ordinalPosition
    ) {
        $crawler = self::$client->request('GET', '/basic.html');
        $crawlerFactory = CrawlerFactory::create($crawler);

        $elementLocator = new ElementLocator(
            LocatorType::CSS_SELECTOR,
            $cssLocator,
            $ordinalPosition
        );

        try {
            $crawlerFactory->createElementCrawler($elementLocator, $crawler);
            $this->fail('InvalidPositionExceptionInterface instance not thrown');
        } catch (InvalidElementPositionException $invalidElementPositionException) {
            $this->assertSame($elementLocator, $invalidElementPositionException->getElementLocator());

            $previousException = $invalidElementPositionException->getPrevious();
            $this->assertInstanceOf(InvalidPositionExceptionInterface::class, $previousException);

            if ($previousException instanceof InvalidPositionExceptionInterface) {
                $this->assertSame($previousException->getOrdinalPosition(), $elementLocator->getOrdinalPosition());
            }
        }
    }

    public function findElementThrowsInvalidPositionExceptionDataProvider(): array
    {
        return [
            'ordinalPosition zero, collection count non-zero' => [
                'cssLocator' => 'h1',
                'ordinalPosition' => 0,
            ],
            'ordinalPosition greater than collection count' => [
                'cssLocator' => 'h1',
                'ordinalPosition' => 3,
            ],
            'ordinalPosition less than negative collection count' => [
                'cssLocator' => 'h1',
                'ordinalPosition' => -3,
            ],
        ];
    }
}
