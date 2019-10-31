<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Services;

use Symfony\Component\Panther\Client as PantherClient;

class PantherClientFactory
{
    public function create(string $baseUri): PantherClient
    {
        return PantherClient::createChromeClient(null, null, [], $baseUri);
    }

    public function destroyClient(PantherClient $client): void
    {
        $client->quit(false);
        $client->getBrowserManager()->quit();
    }
}
