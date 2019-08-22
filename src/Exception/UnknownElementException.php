<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

class UnknownElementException extends AbstractElementException
{
    private $scopeLocator;

    public function __construct(ElementLocator $elementLocator)
    {
        parent::__construct($elementLocator, 'Unknown element "' . $elementLocator->getLocator() . '"');
    }

    public function setScopeLocator(ElementLocator $scopeLocator)
    {
        $this->scopeLocator = $scopeLocator;
    }

    public function getScopeLocator(): ?ElementLocator
    {
        return $this->scopeLocator;
    }
}
