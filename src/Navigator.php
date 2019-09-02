<?php

namespace webignition\SymfonyDomCrawlerNavigator;

use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;
use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;
use webignition\WebDriverElementCollection\WebDriverElementCollection;
use webignition\WebDriverElementCollection\WebDriverElementCollectionInterface;

class Navigator
{
    private $crawler;
    private $crawlerFactory;

    public function __construct(Crawler $crawler, CrawlerFactory $crawlerFactory)
    {
        $this->crawler = $crawler;
        $this->crawlerFactory = $crawlerFactory;
    }

    public static function create(Crawler $crawler): Navigator
    {
        return new Navigator(
            $crawler,
            CrawlerFactory::create()
        );
    }

    public function setCrawler(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * @param ElementLocator $elementLocator
     * @param ElementLocator|null $scopeLocator
     *
     * @return WebDriverElementCollectionInterface
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     */
    public function find(
        ElementLocator $elementLocator,
        ?ElementLocator $scopeLocator = null
    ): WebDriverElementCollectionInterface {
        try {
            return $this->doFind($elementLocator, $scopeLocator);
        } catch (UnknownElementException $unknownElementException) {
            if ($scopeLocator instanceof ElementLocator) {
                $unknownElementException->setScopeLocator($scopeLocator);
            }

            throw $unknownElementException;
        }
    }

    /**
     * @param ElementLocator $elementLocator
     * @param ElementLocator|null $scopeLocator
     *
     * @return bool
     */
    public function has(ElementLocator $elementLocator, ?ElementLocator $scopeLocator = null): bool
    {
        try {
            $collection = $this->doFind($elementLocator, $scopeLocator);

            return count($collection) > 0;
        } catch (UnknownElementException | InvalidElementPositionException $exception) {
            return false;
        }
    }

    /**
     * @param ElementLocator $elementLocator
     * @param ElementLocator $scopeLocator
     *
     * @return WebDriverElementCollectionInterface
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     */
    private function doFind(
        ElementLocator $elementLocator,
        ?ElementLocator $scopeLocator = null
    ): WebDriverElementCollectionInterface {
        $scopeCrawler = $scopeLocator instanceof ElementLocator
            ? $this->crawlerFactory->createSingleElementCrawler($scopeLocator, $this->crawler)
            : $this->crawler;

        $elementCrawler = $this->crawlerFactory->createElementCrawler($elementLocator, $scopeCrawler);

        $elements = [];

        foreach ($elementCrawler as $remoteWebElement) {
            $elements[] = $remoteWebElement;
        }

        return new WebDriverElementCollection($elements);
    }
}
