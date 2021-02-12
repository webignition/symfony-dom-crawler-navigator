<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Unit;

use webignition\SymfonyDomCrawlerNavigator\CollectionPositionFinder;
use webignition\SymfonyDomCrawlerNavigator\Exception\PositionCannotBeZeroException;
use webignition\SymfonyDomCrawlerNavigator\Exception\PositionOutOfBoundsException;

class CollectionPositionFinderTest extends \PHPUnit\Framework\TestCase
{
    private CollectionPositionFinder $collectionPositionFinder;

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
    ): void {
        $this->assertSame($expectedPosition, $this->collectionPositionFinder->find($ordinalPosition, $collectionCount));
    }

    /**
     * @return array<mixed>
     */
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
        \Exception $expectedException
    ): void {
        $this->expectExceptionObject($expectedException);

        $this->collectionPositionFinder->find($ordinalPosition, $collectionCount);
    }

    /**
     * @return array<mixed>
     */
    public function findThrowsExceptionDataProvider(): array
    {
        return [
            'ordinalPosition: zero, collectionCount: non-zero' => [
                'ordinalPosition' => 0,
                'collectionCount' => 1,
                'expectedException' => new PositionCannotBeZeroException(1),
            ],
            'ordinalPosition: positive, collectionCount: zero' => [
                'ordinalPosition' => 1,
                'collectionCount' => 0,
                'expectedException' => new PositionOutOfBoundsException(1, 0),
            ],
            'ordinalPosition greater than collection count, collection count non-zero' => [
                'ordinalPosition' => 3,
                'collectionCount' => 2,
                'expectedException' => new PositionOutOfBoundsException(3, 2),
            ],
            'ordinalPosition greater than collection -count, collection count non-zero' => [
                'ordinalPosition' => -3,
                'collectionCount' => 2,
                'expectedException' => new PositionOutOfBoundsException(-3, 2),
            ],
        ];
    }
}
