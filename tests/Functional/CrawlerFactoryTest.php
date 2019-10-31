<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\DomElementLocator\ElementLocator;
use webignition\DomElementLocator\ElementLocatorInterface;
use webignition\SymfonyDomCrawlerNavigator\CrawlerFactory;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;

class CrawlerFactoryTest extends AbstractTestCase
{
    /**
     * @dataProvider createElementCrawlerSuccessDataProvider
     */
    public function testCreateElementCrawlerSuccess(ElementLocatorInterface $elementLocator, callable $assertions)
    {
        $crawler = self::$pantherClientContainer->get()->request('GET', '/basic.html');

        $crawlerFactory = CrawlerFactory::create();

        $elementCrawler = $crawlerFactory->createElementCrawler($elementLocator, $crawler);

        $assertions($elementCrawler);
    }

    public function createElementCrawlerSuccessDataProvider(): array
    {
        return [
            'first h1 with css selector, position null' => [
                'elementLocator' => new ElementLocator('h1'),
                'assertions' => function (Crawler $crawler) {
                    $this->assertCount(2, $crawler);

                    $expectedElementGetText = [
                        'Hello',
                        'Main',
                    ];

                    /* @var WebDriverElement $element */
                    foreach ($crawler as $index => $element) {
                        if ($element instanceof WebDriverElement) {
                            $this->assertSame($expectedElementGetText[$index], $element->getText());
                        }
                    }
                },
            ],
            'first h1 with css selector, position 1' => [
                'elementLocator' => new ElementLocator('h1', 1),
                'assertions' => function (Crawler $crawler) {
                    $this->assertCount(1, $crawler);
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
            'first h1 with xpath expression' => [
                'elementLocator' => new ElementLocator('//h1', 1),
                'assertions' => function (Crawler $crawler) {
                    $this->assertCount(1, $crawler);
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
            'second h1 with css selector' => [
                'elementLocator' => new ElementLocator('h1', 2),
                'assertions' => function (Crawler $crawler) {
                    $this->assertCount(1, $crawler);
                    $this->assertSame('Main', $crawler->getText());
                },
            ],
        ];
    }

    /**
     * @dataProvider createSingleElementCrawlerSuccessDataProvider
     */
    public function testCreateSingleElementCrawlerSuccess(ElementLocatorInterface $elementLocator, callable $assertions)
    {
        $crawler = self::$pantherClientContainer->get()->request('GET', '/basic.html');
        $crawlerFactory = CrawlerFactory::create();

        $elementCrawler = $crawlerFactory->createSingleElementCrawler($elementLocator, $crawler);
        $this->assertCount(1, $elementCrawler);

        $assertions($elementCrawler);
    }

    public function createSingleElementCrawlerSuccessDataProvider(): array
    {
        return [
            'first h1 with css selector, position null' => [
                'elementLocator' => new ElementLocator('h1'),
                'assertions' => function (Crawler $crawler) {
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
            'first h1 with css selector, position 1' => [
                'elementLocator' => new ElementLocator('h1', 1),
                'assertions' => function (Crawler $crawler) {
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
        ];
    }

    public function testCreateElementCrawlerThrowsUnknownElementException()
    {
        $crawler = self::$pantherClientContainer->get()->request('GET', '/basic.html');
        $crawlerFactory = CrawlerFactory::create();

        $elementLocator = new ElementLocator('.does-not-exist', 1);

        try {
            $crawlerFactory->createElementCrawler($elementLocator, $crawler);
            $this->fail('UnknownElementException not thrown');
        } catch (UnknownElementException $unknownElementException) {
            $this->assertSame($elementLocator, $unknownElementException->getElementLocator());
        }
    }

    /**
     * @dataProvider createElementCrawlerThrowsInvalidElementPositionDataProvider
     */
    public function testCreateElementCrawlerThrowsInvalidElementPositionException(
        string $cssLocator,
        int $ordinalPosition
    ) {
        $crawler = self::$pantherClientContainer->get()->request('GET', '/basic.html');
        $crawlerFactory = CrawlerFactory::create();

        $elementLocator = new ElementLocator($cssLocator, $ordinalPosition);

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

    public function createElementCrawlerThrowsInvalidElementPositionDataProvider(): array
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
