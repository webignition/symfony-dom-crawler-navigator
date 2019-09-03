<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;
use webignition\WebDriverElementCollection\WebDriverElementCollectionInterface;

class OverlyBroadLocatorException extends AbstractElementException
{
    private $scopeLocator;
    private $collection;

    public function __construct(
        ElementLocator $elementLocator,
        ?ElementLocator $scopeLocator,
        WebDriverElementCollectionInterface $collection
    ) {
        parent::__construct($elementLocator, 'Overly broad locator "' . $elementLocator->getLocator() . '"');
        $this->scopeLocator = $scopeLocator;
        $this->collection = $collection;
    }

    public function getScopeLocator(): ?ElementLocator
    {
        return $this->scopeLocator;
    }

    public function getCollection(): WebDriverElementCollectionInterface
    {
        return $this->collection;
    }
}
