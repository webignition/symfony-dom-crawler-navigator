<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

class InvalidElementPositionException extends \Exception
{
    private $elementLocator;

    public function __construct(
        ElementLocator $elementLocator,
        InvalidPositionExceptionInterface $invalidPositionException
    ) {
        $message = sprintf(
            'Invalid position "%d" for locator "%s"',
            $elementLocator->getOrdinalPosition(),
            $elementLocator->getLocator()
        );

        parent::__construct($message, 0, $invalidPositionException);

        $this->elementLocator = $elementLocator;
    }

    public function getElementLocator(): ?ElementLocator
    {
        return $this->elementLocator;
    }
}
