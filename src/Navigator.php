<?php

namespace webignition\SymfonyDomCrawlerNavigator;

use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;
use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;
use webignition\SymfonyDomCrawlerNavigator\Model\LocatorType;

class Navigator
{
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
     * @param ElementLocator $elementLocator
     * @param ElementLocator|null $scope
     *
     * @return WebDriverElement
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     */
    public function findElement(ElementLocator $elementLocator, ?ElementLocator $scope = null): WebDriverElement
    {
        if ($scope instanceof ElementLocator) {
            $crawler = $this->createElementCrawler($scope, $this->crawler);
        } else {
            $crawler = $this->crawler;
        }

        $elementCrawler = $this->createElementCrawler($elementLocator, $crawler);

        return $elementCrawler->getElement(0);
    }

    /**
     * @param ElementLocator $elementLocator
     * @param Crawler $crawler
     *
     * @return Crawler
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     */
    private function createElementCrawler(ElementLocator $elementLocator, Crawler $crawler): Crawler
    {
        $locator = $elementLocator->getLocator();

        $collection = $elementLocator->getLocatorType() === LocatorType::CSS_SELECTOR
            ? $crawler->filter($locator)
            : $crawler->filterXPath($locator);

        $collectionCount = count($collection);

        if (0 === $collectionCount) {
            throw new UnknownElementException($elementLocator);
        }

        try {
            $crawlerPosition = $this->collectionPositionFinder->find(
                $elementLocator->getOrdinalPosition(),
                $collectionCount
            );

            return $collection->eq($crawlerPosition);
        } catch (InvalidPositionExceptionInterface $invalidPositionException) {
            throw new InvalidElementPositionException($elementLocator, $invalidPositionException);
        }
    }
}
