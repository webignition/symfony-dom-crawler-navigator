<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\DomElementLocator\ElementLocatorInterface;

class UnknownElementException extends AbstractElementException
{
    private $scopeLocator;

    public function __construct(ElementLocatorInterface $elementLocator)
    {
        parent::__construct($elementLocator, 'Unknown element "' . $elementLocator->getLocator() . '"');
    }

    public function setScopeLocator(ElementLocatorInterface $scopeLocator)
    {
        $this->scopeLocator = $scopeLocator;
    }

    public function getScopeLocator(): ?ElementLocatorInterface
    {
        return $this->scopeLocator;
    }
}
