<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

use function strlen;
use function strpos;
use function substr;

class BaseUrlFinder
{
    /** @var mixed[] */
    private array $server;

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    private function getServer(string $name)
    {
        return $this->server[$name] ?? null;
    }

    /**
     * Auto-detect the base path from the request environment
     *
     * Uses a variety of criteria in order to detect the base URL of the request
     * (i.e., anything additional to the document root).
     *
     * @param array<string> $serverParams
     * @param string        $uriPath      The server request uri component path
     *
     * @return string
     */
    public function findBaseUrl(array $serverParams, string $uriPath): string
    {
        $this->server = $serverParams;

        $filename       = $this->getServer('SCRIPT_FILENAME') ? : '';
        $scriptName     = $this->getServer('SCRIPT_NAME');
        $phpSelf        = $this->getServer('PHP_SELF');
        $origScriptName = $this->getServer('ORIG_SCRIPT_NAME');

        if ($scriptName !== null && basename((string) $scriptName) === $filename) {
            $baseUrl = (string) $scriptName;
        } elseif ($phpSelf !== null && basename((string) $phpSelf) === $filename) {
            $baseUrl = (string) $phpSelf;
        } elseif ($origScriptName !== null && basename((string) $origScriptName) === $filename) {
            // 1and1 shared hosting compatibility.
            $baseUrl = (string) $origScriptName;
        } else {
            // Backtrack up the SCRIPT_FILENAME to find the portion
            // matching PHP_SELF.

            $baseUrl  = '/';
            $basename = basename((string) $filename);

            if ($basename) {
                $path     = ($phpSelf ? trim((string) $phpSelf, '/') : '');
                $basePos  = strpos($path, $basename) ?: 0;
                $baseUrl .= substr($path, 0, $basePos) . $basename;
            }
        }

        // If the baseUrl is empty, then simply return it.
        if (empty($baseUrl)) {
            return '';
        }

        // Full base URL matches.
        if (strpos($uriPath, $baseUrl) === 0) {
            return $baseUrl;
        }

        // Directory portion of base path matches.
        $baseDir = str_replace('\\', '/', dirname($baseUrl));

        if (strpos($uriPath, $baseDir) === 0) {
            return $baseDir;
        }

        $basename = basename($baseUrl);

        // No match whatsoever
        if (empty($basename) || strpos($uriPath, $basename) === false) {
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of the base path. $pos !== 0 makes sure it is not matching a
        // value from PATH_INFO or QUERY_STRING.
        $pos = strpos($uriPath, $baseUrl);

        if ($pos === false || strlen($uriPath) < strlen($baseUrl)) {
            return $baseUrl;
        }

        return substr($uriPath, 0, $pos + strlen($baseUrl));
    }
}
