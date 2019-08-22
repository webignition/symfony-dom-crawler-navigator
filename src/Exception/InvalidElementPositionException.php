<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

class InvalidElementPositionException extends AbstractElementException
{
    public function __construct(
        ElementLocator $elementLocator,
        InvalidPositionExceptionInterface $invalidPositionException
    ) {
        $message = sprintf(
            'Invalid position "%d" for locator "%s"',
            $elementLocator->getOrdinalPosition(),
            $elementLocator->getLocator()
        );

        parent::__construct($elementLocator, $message, 0, $invalidPositionException);
    }
}
