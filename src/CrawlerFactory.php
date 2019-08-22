<?php

namespace webignition\SymfonyDomCrawlerNavigator;

use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidPositionExceptionInterface;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;
use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;
use webignition\SymfonyDomCrawlerNavigator\Model\LocatorType;

class CrawlerFactory
{
    private $collectionPositionFinder;

    public function __construct(CollectionPositionFinder $collectionPositionFinder)
    {
        $this->collectionPositionFinder = $collectionPositionFinder;
    }

    public static function create(): CrawlerFactory
    {
        return new CrawlerFactory(
            new CollectionPositionFinder()
        );
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
    public function createElementCrawler(ElementLocator $elementLocator, Crawler $crawler): Crawler
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
