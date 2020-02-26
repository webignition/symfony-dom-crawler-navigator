<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\DomElementIdentifier\ElementIdentifier;
use webignition\DomElementIdentifier\ElementIdentifierInterface;
use webignition\SymfonyDomCrawlerNavigator\CrawlerFactory;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;

class CrawlerFactoryTest extends AbstractBrowserTestCase
{
    /**
     * @dataProvider createElementCrawlerSuccessDataProvider
     */
    public function testCreateElementCrawlerSuccess(ElementIdentifierInterface $elementIdentifier, callable $assertions)
    {
        $crawler = self::$client->request('GET', '/basic.html');

        $crawlerFactory = CrawlerFactory::create();

        $elementCrawler = $crawlerFactory->createElementCrawler($elementIdentifier, $crawler);

        $assertions($elementCrawler);
    }

    public function createElementCrawlerSuccessDataProvider(): array
    {
        return [
            'first h1 with css selector, position null' => [
                'elementLocator' => new ElementIdentifier('h1'),
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
                'elementLocator' => new ElementIdentifier('h1', 1),
                'assertions' => function (Crawler $crawler) {
                    $this->assertCount(1, $crawler);
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
            'first h1 with xpath expression' => [
                'elementLocator' => new ElementIdentifier('//h1', 1),
                'assertions' => function (Crawler $crawler) {
                    $this->assertCount(1, $crawler);
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
            'second h1 with css selector' => [
                'elementLocator' => new ElementIdentifier('h1', 2),
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
    public function testCreateSingleElementCrawlerSuccess(
        ElementIdentifierInterface $elementIdentifier,
        callable $assertions
    ) {
        $crawler = self::$client->request('GET', '/basic.html');
        $crawlerFactory = CrawlerFactory::create();

        $elementCrawler = $crawlerFactory->createSingleElementCrawler($elementIdentifier, $crawler);
        $this->assertCount(1, $elementCrawler);

        $assertions($elementCrawler);
    }

    public function createSingleElementCrawlerSuccessDataProvider(): array
    {
        return [
            'first h1 with css selector, position null' => [
                'elementLocator' => new ElementIdentifier('h1'),
                'assertions' => function (Crawler $crawler) {
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
            'first h1 with css selector, position 1' => [
                'elementLocator' => new ElementIdentifier('h1', 1),
                'assertions' => function (Crawler $crawler) {
                    $this->assertSame('Hello', $crawler->getText());
                },
            ],
            'second h1 with css selector, position 2' => [
                'elementLocator' => new ElementIdentifier('h1', 2),
                'assertions' => function (Crawler $crawler) {
                    $this->assertSame('Main', $crawler->getText());
                },
            ],
        ];
    }

    public function testCreateElementCrawlerThrowsUnknownElementException()
    {
        $crawler = self::$client->request('GET', '/basic.html');
        $crawlerFactory = CrawlerFactory::create();

        $elementIdentifier = new ElementIdentifier('.does-not-exist', 1);

        try {
            $crawlerFactory->createElementCrawler($elementIdentifier, $crawler);
            $this->fail('UnknownElementException not thrown');
        } catch (UnknownElementException $unknownElementException) {
            $this->assertSame($elementIdentifier, $unknownElementException->getElementIdentifier());
        }
    }

    /**
     * @dataProvider createElementCrawlerThrowsInvalidElementPositionDataProvider
     */
    public function testCreateElementCrawlerThrowsInvalidElementPositionException(
        string $cssLocator,
        int $ordinalPosition
    ) {
        $crawler = self::$client->request('GET', '/basic.html');
        $crawlerFactory = CrawlerFactory::create();

        $elementIdentifier = new ElementIdentifier($cssLocator, $ordinalPosition);

        try {
            $crawlerFactory->createElementCrawler($elementIdentifier, $crawler);
            $this->fail('InvalidPositionExceptionInterface instance not thrown');
        } catch (InvalidElementPositionException $invalidElementPositionException) {
            $this->assertSame($elementIdentifier, $invalidElementPositionException->getElementIdentifier());

            $previousException = $invalidElementPositionException->getPrevious();
            $this->assertInstanceOf(InvalidPositionExceptionInterface::class, $previousException);

            if ($previousException instanceof InvalidPositionExceptionInterface) {
                $this->assertSame($previousException->getOrdinalPosition(), $elementIdentifier->getOrdinalPosition());
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
