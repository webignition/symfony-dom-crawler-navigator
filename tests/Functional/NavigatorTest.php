<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Functional;

use Facebook\WebDriver\WebDriverElement;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;
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
    public function testFindElementSuccess(
        ElementLocator $elementIdentifier,
        ?ElementLocator $scope,
        callable $assertions
    ) {
        $crawler = self::$client->request('GET', '/basic.html');
        $navigator = Navigator::create($crawler);

        $element = $navigator->findElement($elementIdentifier, $scope);

        $assertions($element);
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
                'scopeLocator' => null,
                'assertions' => function (WebDriverElement $element) {
                    $this->assertSame('Hello', $element->getText());
                },
            ],
            'first h1 with xpath expression' => [
                'elementIdentifier' => new ElementLocator(
                    LocatorType::XPATH_EXPRESSION,
                    '//h1',
                    1
                ),
                'scopeLocator' => null,
                'assertions' => function (WebDriverElement $element) {
                    $this->assertSame('Hello', $element->getText());
                },
            ],
            'second h1 with css selector' => [
                'elementIdentifier' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'h1',
                    2
                ),
                'scopeLocator' => null,
                'assertions' => function (WebDriverElement $element) {
                    $this->assertSame('Main', $element->getText());
                },
            ],
            'css-selector input scoped to css-selector second form' => [
                'elementIdentifier' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'input',
                    1
                ),
                'scopeLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'form[action="/action2"]',
                    1
                ),
                'assertions' => function (WebDriverElement $element) {
                    $this->assertSame('input-2', $element->getAttribute('name'));
                },
            ],
            'css-selector input scoped to xpath-expression second form' => [
                'elementIdentifier' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'input',
                    1
                ),
                'scopeLocator' => new ElementLocator(
                    LocatorType::XPATH_EXPRESSION,
                    '//form',
                    2
                ),
                'assertions' => function (WebDriverElement $element) {
                    $this->assertSame('input-2', $element->getAttribute('name'));
                },
            ],
        ];
    }

    /**
     * @dataProvider findThrowsUnknownElementExceptionDataProvider
     */
    public function testFindElementThrowsUnknownElementException(
        ElementLocator $elementLocator,
        ?ElementLocator $scopeLocator,
        ElementLocator $expectedExceptionElementLocator,
        ?ElementLocator $expectedExceptionScopeLocator
    ) {
        $crawler = self::$client->request('GET', '/basic.html');
        $navigator = Navigator::create($crawler);
//
//        $elementLocator = new ElementLocator(
//            LocatorType::CSS_SELECTOR,
//            '.does-not-exist',
//            1
//        );
//
        try {
            $navigator->findElement($elementLocator, $scopeLocator);
            $this->fail('UnknownElementException not thrown');
        } catch (UnknownElementException $unknownElementException) {
            $this->assertEquals($expectedExceptionElementLocator, $unknownElementException->getElementLocator());
            $this->assertEquals($expectedExceptionScopeLocator, $unknownElementException->getScopeLocator());
        }
    }

    public function findThrowsUnknownElementExceptionDataProvider(): array
    {
        return [
            'element locator refers to unknown element, without scope' => [
                'elementLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    '.does-not-exist',
                    1
                ),
                'scopeLocator' => null,
                'expectedExceptionElementLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    '.does-not-exist',
                    1
                ),
                'expectedExceptionScope' => null,
            ],
            'element locator refers to unknown element, with scope' => [
                'elementLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    '.does-not-exist',
                    1
                ),
                'scopeLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'main',
                    1
                ),
                'expectedExceptionElementLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    '.does-not-exist',
                    1
                ),
                'expectedExceptionScope' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'main',
                    1
                ),
            ],
            'scope locator refers to unknown element' => [
                'elementLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    'input',
                    1
                ),
                'scopeLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    '.does-not-exist',
                    1
                ),
                'expectedExceptionElementLocator' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    '.does-not-exist',
                    1
                ),
                'expectedExceptionScope' => new ElementLocator(
                    LocatorType::CSS_SELECTOR,
                    '.does-not-exist',
                    1
                ),
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
