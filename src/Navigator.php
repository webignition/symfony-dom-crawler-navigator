<?php

namespace webignition\SymfonyDomCrawlerNavigator;

use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;
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
     *
     * @return WebDriverElement
     *
     * @throws InvalidElementPositionException
     */
    public function findElement(ElementLocator $elementLocator): WebDriverElement
    {
        $locator = $elementLocator->getLocator();

        $collection = $elementLocator->getLocatorType() === LocatorType::CSS_SELECTOR
            ? $this->crawler->filter($locator)
            : $this->crawler->filterXPath($locator);

        $collectionCount = count($collection);

        try {
            $crawlerPosition = $this->collectionPositionFinder->find(
                $elementLocator->getOrdinalPosition(),
                $collectionCount
            );

            return $collection->getElement($crawlerPosition);
        } catch (InvalidPositionExceptionInterface $invalidPositionException) {
            throw new InvalidElementPositionException($elementLocator, $invalidPositionException);
        }
    }
}
