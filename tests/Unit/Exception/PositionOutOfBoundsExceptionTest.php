<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Unit\Exception;

use webignition\SymfonyDomCrawlerNavigator\Exception\PositionOutOfBoundsException;

class PositionOutOfBoundsExceptionTest extends \PHPUnit\Framework\TestCase
{
    const COLLECTION_COUNT = 3;
    const ORDINAL_POSITION = 4;

    /**
     * @var PositionOutOfBoundsException
     */
    private $exception;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exception = new PositionOutOfBoundsException(self::ORDINAL_POSITION, self::COLLECTION_COUNT);
    }

    public function testGetOrdinalPosition()
    {
        $this->assertSame(self::ORDINAL_POSITION, $this->exception->getOrdinalPosition());
    }

    public function testGetCollectionCount()
    {
        $this->assertSame(self::COLLECTION_COUNT, $this->exception->getCollectionCount());
    }
}
