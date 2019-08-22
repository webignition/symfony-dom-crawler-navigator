<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

class UnknownElementException extends AbstractElementException
{
    private $elementLocator;

    public function __construct(ElementLocator $elementLocator)
    {
        parent::__construct($elementLocator, 'Unknown element "' . $elementLocator->getLocator() . '"');
    }
}
