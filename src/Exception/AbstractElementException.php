<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\DomElementLocator\ElementLocatorInterface;

abstract class AbstractElementException extends \Exception
{
    private $elementLocator;

    public function __construct(
        ElementLocatorInterface $elementLocator,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->elementLocator = $elementLocator;
    }

    public function getElementLocator(): ElementLocatorInterface
    {
        return $this->elementLocator;
    }
}
