<?php

namespace webignition\SymfonyDomCrawlerNavigator\Model;

class ElementLocator
{
    private $locatorType;
    private $locator;
    private $ordinalPosition;

    public function __construct(string $locatorType, string $locator, ?int $ordinalPosition = null)
    {
        $this->locatorType = $locatorType;
        $this->locator = $locator;
        $this->ordinalPosition = $ordinalPosition;
    }

    public function getLocatorType(): string
    {
        return $this->locatorType;
    }

    public function getLocator(): string
    {
        return $this->locator;
    }

    public function getOrdinalPosition(): ?int
    {
        return $this->ordinalPosition;
    }
}
