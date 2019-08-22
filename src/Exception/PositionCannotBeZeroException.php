<?php

namespace webignition\SymfonyDomCrawlerNavigator\Exception;

class PositionCannotBeZeroException extends AbstractInvalidPositionException
{
    public function __construct(int $collectionCount)
    {
        $message = sprintf(
            'Position cannot be zero. Allowable positions: +/-%s',
            $collectionCount
        );

        parent::__construct(0, $collectionCount, $message);
    }
}
