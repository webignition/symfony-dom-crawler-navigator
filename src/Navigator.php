<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator;

use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\DomElementLocator\ElementLocatorInterface;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\OverlyBroadLocatorException;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;
use webignition\WebDriverElementCollection\RadioButtonCollection;
use webignition\WebDriverElementCollection\SelectOptionCollection;
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
     * @param ElementLocatorInterface $elementLocator
     * @param ElementLocatorInterface|null $scopeLocator
     *
     * @return WebDriverElementCollectionInterface
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     */
    public function find(
        ElementLocatorInterface $elementLocator,
        ?ElementLocatorInterface $scopeLocator = null
    ): WebDriverElementCollectionInterface {
        try {
            return $this->doFind($elementLocator, $scopeLocator);
        } catch (UnknownElementException $unknownElementException) {
            if ($scopeLocator instanceof ElementLocatorInterface) {
                $unknownElementException->setScopeLocator($scopeLocator);
            }

            throw $unknownElementException;
        }
    }

    /**
     * @param ElementLocatorInterface $elementLocator
     * @param ElementLocatorInterface|null $scopeLocator
     *
     * @return WebDriverElement
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     * @throws OverlyBroadLocatorException
     */
    public function findOne(
        ElementLocatorInterface $elementLocator,
        ?ElementLocatorInterface $scopeLocator = null
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
     * @param ElementLocatorInterface $elementLocator
     * @param ElementLocatorInterface|null $scopeLocator
     *
     * @return bool
     */
    public function has(ElementLocatorInterface $elementLocator, ?ElementLocatorInterface $scopeLocator = null): bool
    {
        $examiner = function (WebDriverElementCollectionInterface $collection) {
            return count($collection) > 0;
        };

        return $this->examineCollectionCount($elementLocator, $scopeLocator, $examiner);
    }

    /**
     * @param ElementLocatorInterface $elementLocator
     * @param ElementLocatorInterface|null $scopeLocator
     *
     * @return bool
     */
    public function hasOne(ElementLocatorInterface $elementLocator, ?ElementLocatorInterface $scopeLocator = null): bool
    {
        $examiner = function (WebDriverElementCollectionInterface $collection) {
            return count($collection) === 1;
        };

        return $this->examineCollectionCount($elementLocator, $scopeLocator, $examiner);
    }

    /**
     * @param ElementLocatorInterface $elementLocator
     * @param ElementLocatorInterface|null $scopeLocator
     * @param callable $examiner
     *
     * @return bool
     */
    private function examineCollectionCount(
        ElementLocatorInterface $elementLocator,
        ?ElementLocatorInterface $scopeLocator,
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
     * @param ElementLocatorInterface $elementLocator
     * @param ElementLocatorInterface $scopeLocator
     *
     * @return WebDriverElementCollectionInterface
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     */
    private function doFind(
        ElementLocatorInterface $elementLocator,
        ?ElementLocatorInterface $scopeLocator = null
    ): WebDriverElementCollectionInterface {
        $scopeCrawler = $scopeLocator instanceof ElementLocatorInterface
            ? $this->crawlerFactory->createSingleElementCrawler($scopeLocator, $this->crawler)
            : $this->crawler;

        $elementCrawler = $this->crawlerFactory->createElementCrawler($elementLocator, $scopeCrawler);

        $elements = [];

        foreach ($elementCrawler as $remoteWebElement) {
            $elements[] = $remoteWebElement;
        }

        if (RadioButtonCollection::is($elements)) {
            return new RadioButtonCollection($elements);
        }

        if (SelectOptionCollection::is($elements)) {
            return new SelectOptionCollection($elements);
        }

        return new WebDriverElementCollection($elements);
    }
}
