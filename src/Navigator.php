<?php

namespace webignition\SymfonyDomCrawlerNavigator;

use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\OverlyBroadLocatorException;
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
     * @return WebDriverElement
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     * @throws OverlyBroadLocatorException
     */
    public function findOne(
        ElementLocator $elementLocator,
        ?ElementLocator $scopeLocator = null
    ): WebDriverElement {
        $collection = $this->find($elementLocator, $scopeLocator);

        if (1 === count($collection)) {
            $element = $collection->get(0);

            if ($element instanceof WebDriverElement) {
                return $element;
            }
        }

        throw new OverlyBroadLocatorException($elementLocator, $scopeLocator, $collection);
    }

    /**
     * @param ElementLocator $elementLocator
     * @param ElementLocator|null $scopeLocator
     *
     * @return bool
     */
    public function has(ElementLocator $elementLocator, ?ElementLocator $scopeLocator = null): bool
    {
        $examiner = function (WebDriverElementCollectionInterface $collection) {
            return count($collection) > 0;
        };

        return $this->examineCollectionCount($elementLocator, $scopeLocator, $examiner);
    }

    /**
     * @param ElementLocator $elementLocator
     * @param ElementLocator|null $scopeLocator
     *
     * @return bool
     */
    public function hasOne(ElementLocator $elementLocator, ?ElementLocator $scopeLocator = null): bool
    {
        $examiner = function (WebDriverElementCollectionInterface $collection) {
            return count($collection) === 1;
        };

        return $this->examineCollectionCount($elementLocator, $scopeLocator, $examiner);
    }

    /**
     * @param ElementLocator $elementLocator
     * @param ElementLocator|null $scopeLocator
     * @param callable $examiner
     *
     * @return bool
     */
    private function examineCollectionCount(
        ElementLocator $elementLocator,
        ?ElementLocator $scopeLocator,
        callable $examiner
    ): bool {
        try {
            $collection = $this->doFind($elementLocator, $scopeLocator);

            return $examiner($collection);
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
