<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

class PositionOutOfBoundsException extends AbstractInvalidPositionException
{
    public function __construct(int $ordinalPosition, int $collectionCount)
    {
        $message = sprintf(
            'Position "%i" out of bounds for collection of size "%i". Allowable positions: +/-%i',
            $ordinalPosition,
            $collectionCount,
            $collectionCount
        );

        parent::__construct($ordinalPosition, $collectionCount, $message);
    }
}
