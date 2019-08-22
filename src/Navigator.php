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
    public function findElement(ElementLocator $elementLocator, ?ElementLocator $scopeLocator = null): WebDriverElement
    {
        try {
            $element = $this->doFindElement($elementLocator, $scopeLocator);

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
    public function hasElement(ElementLocator $elementLocator, ?ElementLocator $scopeLocator = null): bool
    {
        try {
            return $this->doFindElement($elementLocator, $scopeLocator) instanceof WebDriverElement;
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
    private function doFindElement(
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
