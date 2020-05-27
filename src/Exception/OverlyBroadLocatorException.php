<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\DomElementIdentifier\ElementIdentifierInterface;
use webignition\WebDriverElementCollection\WebDriverElementCollectionInterface;

class OverlyBroadLocatorException extends AbstractElementException
{
    private WebDriverElementCollectionInterface $collection;

    public function __construct(
        ElementIdentifierInterface $elementIdentifier,
        WebDriverElementCollectionInterface $collection
    ) {
        parent::__construct($elementIdentifier, 'Overly broad locator "' . $elementIdentifier->getLocator() . '"');
        $this->collection = $collection;
    }

    public function getCollection(): WebDriverElementCollectionInterface
    {
        return $this->collection;
    }
}
