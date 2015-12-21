<?php

namespace Blast\BaseUrl;

class BasePathHelper
{
    /** @var string */
    private $basePath;

    /**
     * BasePathHelper constructor.
     * @param string $basePath
     */
    public function __construct($basePath = '')
    {
        $this->setBasePath($basePath);
    }

    /**
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function __invoke($assetUrl = '')
    {
        return $this->basePath . '/' . ltrim($assetUrl, '/');
    }
}
