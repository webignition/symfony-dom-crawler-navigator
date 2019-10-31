<?php

namespace webignition\SymfonyDomCrawlerNavigator\Tests\Services;

use Symfony\Component\Panther\ProcessManager\WebServerManager;

class WebServerRunner
{
    const DEFAULT_HOSTNAME = '127.0.0.1';
    const DEFAULT_PORT = 9080;

    private $webServerDir;

    /**
     * @var array
     */
    private static $defaultOptions = [
        'hostname' => self::DEFAULT_HOSTNAME,
        'port' => self::DEFAULT_PORT,
    ];

    /**
     * @var WebServerManager|null
     */
    private $webServerManager;

    public function __construct(string $webServerDir)
    {
        $this->webServerDir = $webServerDir;
    }

    public function start(): void
    {
        if (null === $this->webServerManager) {
            $this->webServerManager = $this->createWebServerManager();
            $this->webServerManager->start();
        }
    }

    public function stop()
    {
        if ($this->webServerManager instanceof WebServerManager) {
            $this->webServerManager->quit();
            $this->webServerManager = null;
        }
    }

    private function createWebServerManager()
    {
        return new WebServerManager(
            $this->webServerDir,
            self::$defaultOptions['hostname'],
            self::$defaultOptions['port']
        );
    }
}
