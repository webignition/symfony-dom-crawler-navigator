<?php

namespace webignition\SymfonyDomCrawlerNavigator;

use webignition\SymfonyDomCrawlerNavigator\Exception\PositionCannotBeZeroException;
use webignition\SymfonyDomCrawlerNavigator\Exception\PositionOutOfBoundsException;

class CollectionPositionFinder
{
    /**
     * @param int $ordinalPosition
     * @param int $collectionCount
     *
     * @return int
     *
     * @throws PositionCannotBeZeroException
     * @throws PositionOutOfBoundsException
     */
    public function find(int $ordinalPosition, int $collectionCount): int
    {
        if (0 === $ordinalPosition) {
            throw new PositionCannotBeZeroException($collectionCount);
        }

        if ($ordinalPosition < 0) {
            $positiveOrdinalPosition = $collectionCount + $ordinalPosition + 1;

            if ($positiveOrdinalPosition < 1 || $positiveOrdinalPosition > $collectionCount) {
                throw new PositionOutOfBoundsException($ordinalPosition, $collectionCount);
            }

            $ordinalPosition = $positiveOrdinalPosition;
        }

        if ($ordinalPosition > $collectionCount) {
            throw new PositionOutOfBoundsException($ordinalPosition, $collectionCount);
        }

        return $ordinalPosition - 1;
    }
}
