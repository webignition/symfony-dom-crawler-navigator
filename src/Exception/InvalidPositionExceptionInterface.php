<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

interface InvalidPositionExceptionInterface extends \Throwable
{
    public function getOrdinalPosition(): int;
    public function getCollectionCount(): int;
}
