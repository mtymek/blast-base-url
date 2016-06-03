<?php

namespace Blast\BaseUrl;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Helper\UrlHelper;

class BaseUrlMiddleware
{
    const BASE_URL = '_base_url';
    const BASE_PATH = '_base_path';

    /**
     * @var BaseUrlFinder
     */
    private $baseUrlFinder;

    /** @var UrlHelper */
    private $urlHelper;

    /** @var BasePathHelper  */
    private $basePathHelper;

    /**
     * BaseUrlMiddleware constructor.
     */
    public function __construct()
    {
        $this->baseUrlFinder = new BaseUrlFinder();
    }

    /**
     * @param UrlHelper $urlHelper
     */
    public function setUrlHelper($urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param BasePathHelper $basePathHelper
     */
    public function setBasePathHelper($basePathHelper)
    {
        $this->basePathHelper = $basePathHelper;
    }

    /**
     * Autodetect the base path of the request
     *
     * Uses several criteria to determine the base path of the request.
     *
     * @return string
     */
    private function detectBasePath($serverParams, $baseUrl)
    {
        // Empty base url detected
        if ($baseUrl === '') {
            return '';
        }

        $filename = basename(isset($serverParams['SCRIPT_FILENAME']) ? $serverParams['SCRIPT_FILENAME'] : '');

        // basename() matches the script filename; return the directory
        if (basename($baseUrl) === $filename) {
            return str_replace('\\', '/', dirname($baseUrl));
        }

        // Base path is identical to base URL
        return $baseUrl;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $uri = $request->getUri();
        $uriPath = $uri->getPath();

        $baseUrl  = $this->baseUrlFinder->findBaseUrl($request->getServerParams(), $uriPath);
        $basePath = $this->detectBasePath($request->getServerParams(), $baseUrl);

        $request = $request->withAttribute(self::BASE_URL, $baseUrl);
        $request = $request->withAttribute(self::BASE_PATH, $basePath);

        if (!empty($baseUrl) && strpos($uriPath, $baseUrl) === 0) {
            $path = substr($uriPath, strlen($baseUrl));
            $path = '/' . ltrim($path, '/');
            $request = $request->withUri($uri->withPath($path));
        }

        if ($this->urlHelper) {
            $this->urlHelper->setBasePath($baseUrl);
        }

        if ($this->basePathHelper) {
            $this->basePathHelper->setBasePath($basePath);
        }

        return $next($request, $response);
    }
}
