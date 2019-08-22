<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

interface InvalidPositionExceptionInterface extends \Throwable
{
    public function getOrdinalPosition(): int;
    public function getCollectionCount(): int;
    public function setElementLocator(ElementLocator $elementIdentifier);
    public function getElementLocator(): ?ElementLocator;
}
