<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

class PositionOutOfBoundsException extends AbstractInvalidPositionException
{
    public function __construct(int $ordinalPosition, int $collectionCount)
    {
        $message = sprintf(
            'Position "%d" out of bounds for collection of size "%d". Allowable positions: +/-%d',
            $ordinalPosition,
            $collectionCount,
            $collectionCount
        );

        parent::__construct($ordinalPosition, $collectionCount, $message);
    }
}
