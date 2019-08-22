<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

abstract class AbstractElementException extends \Exception
{
    private $elementLocator;

    public function __construct(
        ElementLocator $elementLocator,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->elementLocator = $elementLocator;
    }

    public function getElementLocator(): ElementLocator
    {
        return $this->elementLocator;
    }
}
