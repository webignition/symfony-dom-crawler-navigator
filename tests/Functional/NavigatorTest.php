<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;
use webignition\SymfonyDomCrawlerNavigator\Navigator;

class NavigatorTest extends TestCase
{
    public function testCreate()
    {
        $crawler = self::$client->request('GET', '/index.html');
        $navigator = Navigator::create($crawler);

        $this->assertInstanceOf(Navigator::class, $navigator);
    }

    /**
     * @dataProvider findElementSuccessDataProvider
     */
    public function testFindElementSuccess(
        string $locatorType,
        string $locator,
        int $ordinalPosition,
        string $expectedText
    ) {
        $crawler = self::$client->request('GET', '/basic.html');
        $navigator = Navigator::create($crawler);

        $element = $navigator->findElement($locatorType, $locator, $ordinalPosition);

        $this->assertSame($expectedText, $element->getText());
    }

    public function findElementSuccessDataProvider(): array
    {
        return [
            'first h1 with css selector' => [
                'locatorType' => Navigator::LOCATOR_CSS_SELECTOR,
                'locator' => 'h1',
                'ordinalPosition' => 1,
                'expectedText' => 'Hello',
            ],
            'first h1 with xpath expression' => [
                'locatorType' => Navigator::LOCATOR_XPATH_EXPRESSION,
                'locator' => '//h1',
                'ordinalPosition' => 1,
                'expectedText' => 'Hello',
            ],
            'second h1 with css selector' => [
                'locatorType' => Navigator::LOCATOR_CSS_SELECTOR,
                'locator' => 'h1',
                'ordinalPosition' => 2,
                'expectedText' => 'Main',
            ],
        ];
    }

    /**
     * @dataProvider findElementThrowsInvalidPositionExceptionDataProvider
     */
    public function testFindElementThrowsInvalidPositionException(string $cssLocator, int $ordinalPosition)
    {
        $crawler = self::$client->request('GET', '/basic.html');
        $navigator = Navigator::create($crawler);

        try {
            $navigator->findElement(Navigator::LOCATOR_CSS_SELECTOR, $cssLocator, $ordinalPosition);
            $this->fail('InvalidPositionExceptionInterface instance not thrown');
        } catch (InvalidPositionExceptionInterface $invalidPositionException) {
            $this->assertSame($ordinalPosition, $invalidPositionException->getOrdinalPosition());
            $this->assertSame($cssLocator, $invalidPositionException->getLocator());
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
