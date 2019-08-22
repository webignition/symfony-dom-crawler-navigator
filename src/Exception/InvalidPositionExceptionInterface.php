<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

interface InvalidPositionExceptionInterface extends \Throwable
{
    public function getOrdinalPosition(): int;
    public function getCollectionCount(): int;
    public function setLocator(string $locator);
    public function getLocator(): string;
}
