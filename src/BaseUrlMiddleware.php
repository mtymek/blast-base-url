<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BaseUrlMiddleware implements MiddlewareInterface
{
    public const BASE_URL  = '_base_url';
    public const BASE_PATH = '_base_path';

    private BaseUrlFinder $baseUrlFinder;

    private ?UrlHelper $urlHelper;

    private ?BasePathHelper $basePathHelper;

    /**
     * BaseUrlMiddleware constructor
     */
    public function __construct()
    {
        $this->baseUrlFinder  = new BaseUrlFinder();
        $this->urlHelper      = null;
        $this->basePathHelper = null;
    }

    public function setUrlHelper(UrlHelper $urlHelper): void
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param BasePathHelper $basePathHelper
     */
    public function setBasePathHelper(BasePathHelper $basePathHelper): void
    {
        $this->basePathHelper = $basePathHelper;
    }

    /**
     * Autodetect the base path of the request
     *
     * Uses several criteria to determine the base path of the request.
     *
     * @param string[] $serverParams
     * @param string   $baseUrl
     *
     * @return string
     */
    private function detectBasePath(array $serverParams, string $baseUrl): string
    {
        // Empty base url detected
        if ($baseUrl === '') {
            return '';
        }

        $filename = basename($serverParams['SCRIPT_FILENAME'] ?? '');

        // basename() matches the script filename; return the directory
        if (basename($baseUrl) === $filename) {
            return str_replace('\\', '/', dirname($baseUrl));
        }

        // Base path is identical to base URL
        return $baseUrl;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri     = $request->getUri();
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

        return $handler->handle($request);
    }
}
