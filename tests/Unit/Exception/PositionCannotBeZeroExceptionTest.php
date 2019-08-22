<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Unit\Exception;

use webignition\SymfonyDomCrawlerNavigator\Exception\PositionCannotBeZeroException;
use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;

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

    public function testSetElementIdentifier()
    {
        $this->assertNull($this->exception->getElementLocator());

        $elementLocator = new ElementLocator('', '', 1);
        $this->exception->setElementLocator($elementLocator);

        $this->assertSame($elementLocator, $this->exception->getElementLocator());
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
