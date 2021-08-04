<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

class BasePathHelper
{
    private string $basePath;

    /**
     * BasePathHelper constructor
     *
     * @param string $basePath
     */
    public function __construct(string $basePath = '')
    {
        $this->setBasePath($basePath);
    }

    /**
     * @param string $basePath
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * @param string $assetUrl
     *
     * @return string
     */
    public function __invoke(string $assetUrl = ''): string
    {
        return $this->basePath . '/' . ltrim($assetUrl, '/');
    }
}
