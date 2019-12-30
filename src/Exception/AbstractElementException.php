<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\DomElementIdentifier\ElementIdentifierInterface;
use webignition\DomElementLocator\ElementLocatorInterface;

abstract class AbstractElementException extends \Exception
{
    private $elementIdentifier;

    public function __construct(
        ElementIdentifierInterface $elementIdentifier,
        string $message = '',
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->elementIdentifier = $elementIdentifier;
    }

    public function getElementIdentifier(): ElementLocatorInterface
    {
        return $this->elementIdentifier;
    }
}
