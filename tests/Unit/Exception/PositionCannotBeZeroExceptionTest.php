<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Unit\Exception;

use webignition\SymfonyDomCrawlerNavigator\Exception\PositionCannotBeZeroException;

class PositionCannotBeZeroExceptionTest extends \PHPUnit\Framework\TestCase
{
    private const COLLECTION_COUNT = 3;

    private PositionCannotBeZeroException $exception;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exception = new PositionCannotBeZeroException(self::COLLECTION_COUNT);
    }

    public function testGetOrdinalPosition()
    {
        $this->assertSame(0, $this->exception->getOrdinalPosition());
    }

    public function testGetCollectionCount()
    {
        $this->assertSame(self::COLLECTION_COUNT, $this->exception->getCollectionCount());
    }
}
