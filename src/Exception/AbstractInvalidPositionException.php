<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

abstract class AbstractInvalidPositionException extends \Exception implements InvalidPositionExceptionInterface
{
    private $ordinalPosition;
    private $collectionCount;

    /**
     * @var ElementLocator|null
     */
    private $elementLocator = null;

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
