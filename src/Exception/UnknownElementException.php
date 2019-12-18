<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\DomElementLocator\ElementLocatorInterface;

class UnknownElementException extends AbstractElementException
{
    /**
     * @var ElementLocatorInterface|null
     */
    private $scopeLocator;

    public function __construct(ElementLocatorInterface $elementLocator)
    {
        parent::__construct($elementLocator, 'Unknown element "' . $elementLocator->getLocator() . '"');
    }

    public function setScopeLocator(ElementLocatorInterface $scopeLocator): void
    {
        $this->scopeLocator = $scopeLocator;
    }

    public function getScopeLocator(): ?ElementLocatorInterface
    {
        return $this->scopeLocator;
    }
}
