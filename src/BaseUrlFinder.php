<?php

namespace Blast\BaseUrl;

class BaseUrlFinder
{
    /**
     * @var array
     */
    private $server;

    private function getServer($name)
    {
        if (isset($this->server[$name])) {
            return $this->server[$name];
        }
        return null;
    }

    /**
     * Auto-detect the base path from the request environment
     *
     * Uses a variety of criteria in order to detect the base URL of the request
     * (i.e., anything additional to the document root).
     *
     *
     * @param array $serverParams
     * @param string $uriPath The server request uri component path
     * @return string
     */
    public function findBaseUrl(array $serverParams, $uriPath)
    {
        $this->server = $serverParams;

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

        // Full base URL matches.
        if (0 === strpos($uriPath, $baseUrl)) {
            return $baseUrl;
        }

        // Directory portion of base path matches.
        $baseDir = str_replace('\\', '/', dirname($baseUrl));
        if (0 === strpos($uriPath, $baseDir)) {
            return $baseDir;
        }

        $basename = basename($baseUrl);

        // No match whatsoever
        if (empty($basename) || false === strpos($uriPath, $basename)) {
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of the base path. $pos !== 0 makes sure it is not matching a
        // value from PATH_INFO or QUERY_STRING.
        if (strlen($uriPath) >= strlen($baseUrl)
            && (false !== ($pos = strpos($uriPath, $baseUrl)) && $pos !== 0)
        ) {
            $baseUrl = substr($uriPath, 0, $pos + strlen($baseUrl));
        }

        return $baseUrl;
    }
}
