<?php

namespace webignition\SymfonyDomCrawlerNavigator;

use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;

class Navigator
{
    const LOCATOR_CSS_SELECTOR = 'css-selector';
    const LOCATOR_XPATH_EXPRESSION = 'xpath-expression';

    private $crawler;
    private $collectionPositionFinder;

    public function __construct(Crawler $crawler, CollectionPositionFinder $collectionPositionFinder)
    {
        $this->crawler = $crawler;
        $this->collectionPositionFinder = $collectionPositionFinder;
    }

    public static function create(Crawler $crawler): Navigator
    {
        return new Navigator(
            $crawler,
            new CollectionPositionFinder()
        );
    }

    /**
     * @param string $locatorType
     * @param string $locator
     * @param int $ordinalPosition
     *
     * @return WebDriverElement
     *
     * @throws InvalidPositionExceptionInterface
     */
    public function findElement(
        string $locatorType,
        string $locator,
        int $ordinalPosition
    ): WebDriverElement {
        $collection = $locatorType === self::LOCATOR_CSS_SELECTOR
            ? $this->crawler->filter($locator)
            : $this->crawler->filterXPath($locator);

        $collectionCount = count($collection);

        try {
            $crawlerPosition = $this->collectionPositionFinder->find($ordinalPosition, $collectionCount);

            return $collection->getElement($crawlerPosition);
        } catch (InvalidPositionExceptionInterface $invalidPositionException) {
            $invalidPositionException->setLocator($locator);

            throw $invalidPositionException;
        }
    }
}
