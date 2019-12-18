<?php

declare(strict_types=1);

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

class PositionCannotBeZeroException extends AbstractInvalidPositionException
{
    public function __construct(int $collectionCount)
    {
        $message = sprintf(
            'Position cannot be zero. Allowable positions: +/-%d',
            $collectionCount
        );

        parent::__construct(0, $collectionCount, $message);
    }
}
