<?php

namespace Blast\BaseUrl;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * Autodetect the base path of the request
     *
     * Uses several criteria to determine the base path of the request.
     *
     * @return string
     */
    private function detectBasePath($serverParams, $baseUrl)
    {
        $filename = basename(isset($serverParams['SCRIPT_FILENAME']) ? $serverParams['SCRIPT_FILENAME'] : '');

        // Empty base url detected
        if ($baseUrl === '') {
            return '';
        }

        // basename() matches the script filename; return the directory
        if (basename($baseUrl) === $filename) {
            return str_replace('\\', '/', dirname($baseUrl));
        }

        // Base path is identical to base URL
        return $baseUrl;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $baseUrl = $this->baseUrlFinder->findBaseUrl($request->getServerParams());

        $request = $request->withAttribute(self::BASE_URL, $baseUrl);
        $request = $request->withAttribute(
            self::BASE_PATH,
            $this->detectBasePath($request->getServerParams(), $baseUrl)
        );

        if (!empty($baseUrl) && strpos($request->getUri()->getPath(), $baseUrl) === 0) {
            $path = substr($request->getUri()->getPath(), strlen($baseUrl)) ?: '/';
            $request = $request->withUri($request->getUri()->withPath($path));
        }

        if ($this->urlHelper) {
            $this->urlHelper->setBaseUrl($baseUrl);
        }

        return $next($request, $response);
    }
}
