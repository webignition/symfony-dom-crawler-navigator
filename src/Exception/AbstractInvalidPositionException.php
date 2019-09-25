<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

abstract class AbstractInvalidPositionException extends \Exception implements InvalidPositionExceptionInterface
{
    private $ordinalPosition;
    private $collectionCount;

    public function __construct(int $ordinalPosition, int $collectionCount, string $message)
    {
        $this->ordinalPosition = $ordinalPosition;
        $this->collectionCount = $collectionCount;

        parent::__construct($message);
    }

    public function getOrdinalPosition(): int
    {
        return $this->ordinalPosition;
    }

    public function getCollectionCount(): int
    {
        return $this->collectionCount;
    }
}
