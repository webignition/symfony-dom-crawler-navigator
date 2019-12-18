<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Unit;

use webignition\SymfonyDomCrawlerNavigator\CollectionPositionFinder;
use webignition\SymfonyDomCrawlerNavigator\Exception\PositionCannotBeZeroException;
use webignition\SymfonyDomCrawlerNavigator\Exception\PositionOutOfBoundsException;

class CollectionPositionFinderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CollectionPositionFinder
     */
    private $collectionPositionFinder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collectionPositionFinder = new CollectionPositionFinder();
    }

    /**
     * @dataProvider findSuccessDataProvider
     */
    public function testFindSuccess(
        int $ordinalPosition,
        int $collectionCount,
        int $expectedPosition
    ) {
        $this->assertSame($expectedPosition, $this->collectionPositionFinder->find($ordinalPosition, $collectionCount));
    }

    public function findSuccessDataProvider(): array
    {
        return [
            'first of three' => [
                'ordinalPosition' => 1,
                'collectionCount' => 3,
                'expectedPosition' => 0,
            ],
            'second of three' => [
                'ordinalPosition' => 2,
                'collectionCount' => 3,
                'expectedPosition' => 1,
            ],
            'third of three' => [
                'ordinalPosition' => 3,
                'collectionCount' => 3,
                'expectedPosition' => 2,
            ],
            'last of three' => [
                'ordinalPosition' => -1,
                'collectionCount' => 3,
                'expectedPosition' => 2,
            ],
            'second to last of three' => [
                'ordinalPosition' => -2,
                'collectionCount' => 3,
                'expectedPosition' => 1,
            ],
            'third to last of three' => [
                'ordinalPosition' => -3,
                'collectionCount' => 3,
                'expectedPosition' => 0,
            ],
        ];
    }

    /**
     * @dataProvider findThrowsExceptionDataProvider
     */
    public function testFindThrowsException(
        int $ordinalPosition,
        int $collectionCount,
        string $expectedException
    ) {
        $this->expectException($expectedException);

        $this->collectionPositionFinder->find($ordinalPosition, $collectionCount);
    }

    public function findThrowsExceptionDataProvider(): array
    {
        return [
            'ordinalPosition: zero, collectionCount: non-zero' => [
                'ordinalPosition' => 0,
                'collectionCount' => 1,
                'expectedException' => PositionCannotBeZeroException::class,
            ],
            'ordinalPosition: positive, collectionCount: zero' => [
                'ordinalPosition' => 1,
                'collectionCount' => 0,
                'expectedException' => PositionOutOfBoundsException::class,
            ],
            'ordinalPosition greater than collection count, collection count non-zero' => [
                'ordinalPosition' => 3,
                'collectionCount' => 2,
                'expectedException' => PositionOutOfBoundsException::class,
            ],
            'ordinalPosition greater than collection -count, collection count non-zero' => [
                'ordinalPosition' => -3,
                'collectionCount' => 2,
                'expectedException' => PositionOutOfBoundsException::class,
            ],
        ];
    }
}
