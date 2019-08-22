<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;
use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;
use webignition\SymfonyDomCrawlerNavigator\Model\LocatorType;
use webignition\SymfonyDomCrawlerNavigator\Navigator;

class NavigatorTest extends AbstractTestCase
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
    public function testFindElementSuccess(ElementLocator $elementIdentifier, string $expectedText)
    {
        $crawler = self::$client->request('GET', '/basic.html');
        $navigator = Navigator::create($crawler);

        $element = $navigator->findElement($elementIdentifier);

        $this->assertSame($expectedText, $element->getText());
    }

    public function findElementSuccessDataProvider(): array
    {
        return [
            'first h1 with css selector' => [
                'elementIdentifier' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'h1',
                    1
                ),
                'expectedText' => 'Hello',
            ],
            'first h1 with xpath expression' => [
                'elementIdentifier' => new ElementLocator(
                    LocatorType::XPATH_EXPRESSION,
                    '//h1',
                    1
                ),
                'expectedText' => 'Hello',
            ],
            'second h1 with css selector' => [
                'elementIdentifier' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'h1',
                    2
                ),
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

        $elementLocator = new ElementLocator(
            LocatorType::CSS_SELECTOR,
            $cssLocator,
            $ordinalPosition
        );

        try {
            $navigator->findElement($elementLocator);
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
