<?php

namespace Blast\BasePath;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BasePathMiddleware
{
    const BASE_URL = '_base_url';
    const BASE_PATH = '_base_path';

    /**
     * @var array
     */
    private $server;

    /** @var UrlHelper */
    private $urlHelper;

    /**
     * @param UrlHelper $urlHelper
     */
    public function setUrlHelper($urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    private function getServer($name)
    {
        if (isset($this->server[$name])) {
            return $this->server[$name];
        }
        return null;
    }

    private function detectRequestUri()
    {
        $requestUri = null;

        // Check this first so IIS will catch.
        $httpXRewriteUrl = $this->getServer('HTTP_X_REWRITE_URL');
        if ($httpXRewriteUrl !== null) {
            $requestUri = $httpXRewriteUrl;
        }

        // Check for IIS 7.0 or later with ISAPI_Rewrite
        $httpXOriginalUrl = $this->getServer('HTTP_X_ORIGINAL_URL');
        if ($httpXOriginalUrl !== null) {
            $requestUri = $httpXOriginalUrl;
        }

        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = $this->getServer('IIS_WasUrlRewritten');
        $unencodedUrl    = $this->getServer('UNENCODED_URL') ? : '';
        if ('1' == $iisUrlRewritten && '' !== $unencodedUrl) {
            return $unencodedUrl;
        }

        // HTTP proxy requests setup request URI with scheme and host [and port]
        // + the URL path, only use URL path.
        if (!$httpXRewriteUrl) {
            $requestUri = $this->getServer('REQUEST_URI');
        }

        if ($requestUri !== null) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }

        // IIS 5.0, PHP as CGI.
        $origPathInfo = $this->getServer('ORIG_PATH_INFO');
        if ($origPathInfo !== null) {
            $queryString = $this->getServer('QUERY_STRING') ? : '';
            if ($queryString !== '') {
                $origPathInfo .= '?' . $queryString;
            }
            return $origPathInfo;
        }

        return '/';
    }

    /**
     * Auto-detect the base path from the request environment
     *
     * Uses a variety of criteria in order to detect the base URL of the request
     * (i.e., anything additional to the document root).
     *
     *
     * @return string
     */
    private function detectBaseUrl()
    {
        $filename       = $this->getServer('SCRIPT_FILENAME') ? : '';
        $scriptName     = $this->getServer('SCRIPT_NAME');
        $phpSelf        = $this->getServer('PHP_SELF');
        $origScriptName = $this->getServer('ORIG_SCRIPT_NAME');

        if ($scriptName !== null && basename($scriptName) === $filename) {
            $baseUrl = $scriptName;
        } elseif ($phpSelf !== null && basename($phpSelf) === $filename) {
            $baseUrl = $phpSelf;
        } elseif ($origScriptName !== null && basename($origScriptName) === $filename) {
            // 1and1 shared hosting compatibility.
            $baseUrl = $origScriptName;
        } else {
            // Backtrack up the SCRIPT_FILENAME to find the portion
            // matching PHP_SELF.

            $baseUrl  = '/';
            $basename = basename($filename);
            if ($basename) {
                $path     = ($phpSelf ? trim($phpSelf, '/') : '');
                $basePos  = strpos($path, $basename) ?: 0;
                $baseUrl .= substr($path, 0, $basePos) . $basename;
            }
        }

        // If the baseUrl is empty, then simply return it.
        if (empty($baseUrl)) {
            return '';
        }

        // Does the base URL have anything in common with the request URI?
        $requestUri = $this->detectRequestUri();

        // Full base URL matches.
        if (0 === strpos($requestUri, $baseUrl)) {
            return $baseUrl;
        }

        // Directory portion of base path matches.
        $baseDir = str_replace('\\', '/', dirname($baseUrl));
        if (0 === strpos($requestUri, $baseDir)) {
            return $baseDir;
        }

        $truncatedRequestUri = $requestUri;

        if (false !== ($pos = strpos($requestUri, '?'))) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);

        // No match whatsoever
        if (empty($basename) || false === strpos($truncatedRequestUri, $basename)) {
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of the base path. $pos !== 0 makes sure it is not matching a
        // value from PATH_INFO or QUERY_STRING.
        if (strlen($requestUri) >= strlen($baseUrl)
            && (false !== ($pos = strpos($requestUri, $baseUrl)) && $pos !== 0)
        ) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return $baseUrl;
    }

    /**
     * Autodetect the base path of the request
     *
     * Uses several criteria to determine the base path of the request.
     *
     * @return string
     */
    private function detectBasePath($baseUrl)
    {
        $filename = basename($this->getServer('SCRIPT_FILENAME') ? : '');

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
        $this->server = $request->getServerParams();

        $baseUrl = $this->detectBaseUrl();

        $request = $request->withAttribute(self::BASE_URL, $baseUrl);
        $request = $request->withAttribute(self::BASE_PATH, $this->detectBasePath($baseUrl));

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
