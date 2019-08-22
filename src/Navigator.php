<?php

namespace webignition\SymfonyDomCrawlerNavigator;

use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\DomCrawler\Crawler;
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
     * @param ElementLocator $elementIdentifier
     *
     * @return WebDriverElement
     *
     * @throws InvalidPositionExceptionInterface
     */
    public function findElement(ElementLocator $elementIdentifier): WebDriverElement
    {
        $locator = $elementIdentifier->getLocator();

        $collection = $elementIdentifier->getLocatorType() === LocatorType::CSS_SELECTOR
            ? $this->crawler->filter($locator)
            : $this->crawler->filterXPath($locator);

        $collectionCount = count($collection);

        try {
            $crawlerPosition = $this->collectionPositionFinder->find(
                $elementIdentifier->getOrdinalPosition(),
                $collectionCount
            );

            return $collection->getElement($crawlerPosition);
        } catch (InvalidPositionExceptionInterface $invalidPositionException) {
            $invalidPositionException->setElementLocator($elementIdentifier);

            throw $invalidPositionException;
        }
    }
}
