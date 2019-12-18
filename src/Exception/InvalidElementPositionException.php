<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\DomElementLocator\ElementLocatorInterface;

class InvalidElementPositionException extends AbstractElementException
{
    public function __construct(
        ElementLocatorInterface $elementLocator,
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
