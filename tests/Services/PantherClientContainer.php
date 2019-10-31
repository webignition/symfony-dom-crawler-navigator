<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Services;

use Symfony\Component\Panther\Client as PantherClient;

class PantherClientContainer
{
    /**
     * @var PantherClient
     */
    private $client;

    public function __construct(string $baseUri)
    {
        $this->client = PantherClient::createChromeClient(null, null, [], $baseUri);
    }

    public function get(): PantherClient
    {
        return $this->client;
    }

    public function destroy(): void
    {
        $this->client->quit(false);
        $this->client->getBrowserManager()->quit();
    }
}
