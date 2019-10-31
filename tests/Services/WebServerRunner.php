<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Services;

use Symfony\Component\Panther\ProcessManager\WebServerManager;

class WebServerRunner
{
    private $webServerDir;
    private $options = [];

    /**
     * @var WebServerManager|null
     */
    private $webServerManager;

    public function __construct(string $webServerDir, array $options = [])
    {
        $this->webServerDir = $webServerDir;
        $this->options = array_merge(Options::getDefault(), $options);
    }

    public function start(): void
    {
        if (null === $this->webServerManager) {
            $this->webServerManager = $this->createWebServerManager();
            $this->webServerManager->start();
        }
    }

    public function stop(): void
    {
        if ($this->webServerManager instanceof WebServerManager) {
            $this->webServerManager->quit();
            $this->webServerManager = null;
        }
    }

    private function createWebServerManager(): WebServerManager
    {
        return new WebServerManager(
            $this->webServerDir,
            $this->options['hostname'],
            $this->options['port']
        );
    }
}
