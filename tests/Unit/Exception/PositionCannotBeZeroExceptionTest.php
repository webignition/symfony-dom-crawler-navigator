<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Unit\Exception;

use webignition\SymfonyDomCrawlerNavigator\Exception\PositionCannotBeZeroException;

class PositionCannotBeZeroExceptionTest extends \PHPUnit\Framework\TestCase
{
    const COLLECTION_COUNT = 3;

    /**
     * @var PositionCannotBeZeroException
     */
    private $exception;

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
