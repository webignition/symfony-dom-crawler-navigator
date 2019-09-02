<?php

namespace webignition\SymfonyDomCrawlerNavigator\Model;

use Facebook\WebDriver\WebDriverElement;

class WebDriverElementCollection implements \Countable, \Iterator
{
    /**
     * @var WebDriverElement[]
     */
    private $elements = [];

    private $iteratorIndex = [];
    private $iteratorPosition = 0;

    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            if ($element instanceof WebDriverElement) {
                $this->elements[] = $element;
            }
        }
    }

    public function get(int $index): ?WebDriverElement
    {
        return $this->elements[$index] ?? null;
    }

    // Countable methods

    public function count(): int
    {
        return count($this->elements);
    }

    // Iterator methods

    public function rewind()
    {
        $this->iteratorPosition = 0;
    }

    public function current(): WebDriverElement
    {
        $key = $this->iteratorIndex[$this->iteratorPosition];

        return $this->elements[$key];
    }

    public function key(): string
    {
        return $this->iteratorIndex[$this->iteratorPosition];
    }

    public function next()
    {
        ++$this->iteratorPosition;
    }

    public function valid(): bool
    {
        $key = $this->iteratorIndex[$this->iteratorPosition] ?? null;

        return $key !== null;
    }
}
