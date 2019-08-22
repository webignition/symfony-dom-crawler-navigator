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

    /**
     * @param ElementLocator $elementLocator
     * @param ElementLocator|null $scope
     *
     * @return WebDriverElement|null
     *
     * @throws InvalidElementPositionException
     * @throws UnknownElementException
     */
    public function findElement(ElementLocator $elementLocator, ?ElementLocator $scope = null): ?WebDriverElement
    {
        $scopeCrawler = $scope instanceof ElementLocator
            ? $this->crawlerFactory->createElementCrawler($scope, $this->crawler)
            : $this->crawler;

        $elementCrawler = $this->crawlerFactory->createElementCrawler($elementLocator, $scopeCrawler);

        return $elementCrawler->getElement(0);
    }
}
