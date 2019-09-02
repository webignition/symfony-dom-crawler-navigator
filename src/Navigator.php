<?php

namespace webignition\SymfonyDomCrawlerNavigator;

use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\Exception\InvalidElementPositionException;
use webignition\SymfonyDomCrawlerNavigator\Exception\UnknownElementException;
use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

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
     * @return WebDriverElement
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     */
    public function find(ElementLocator $elementLocator, ?ElementLocator $scopeLocator = null): WebDriverElement
    {
        try {
            $element = $this->doFind($elementLocator, $scopeLocator);

            if ($element instanceof WebDriverElement) {
                return $element;
            }
        } catch (UnknownElementException $unknownElementException) {
            if ($scopeLocator instanceof ElementLocator) {
                $unknownElementException->setScopeLocator($scopeLocator);
            }

            throw $unknownElementException;
        }

        throw new UnknownElementException($elementLocator);
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
            return $this->doFind($elementLocator, $scopeLocator) instanceof WebDriverElement;
        } catch (UnknownElementException | InvalidElementPositionException $exception) {
            return false;
        }
    }

    /**
     * @param ElementLocator $elementLocator
     * @param ElementLocator $scopeLocator
     *
     * @return WebDriverElement|null
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     */
    private function doFind(
        ElementLocator $elementLocator,
        ?ElementLocator $scopeLocator = null
    ): ?WebDriverElement {
        $scopeCrawler = $scopeLocator instanceof ElementLocator
            ? $this->crawlerFactory->createElementCrawler($scopeLocator, $this->crawler)
            : $this->crawler;

        $elementCrawler = $this->crawlerFactory->createElementCrawler($elementLocator, $scopeCrawler);

        return $elementCrawler->getElement(0);
    }
}
