<?php

namespace Blast\BasePath;

use Zend\Expressive\Helper\UrlHelper as BaseHelper;

class UrlHelper extends BaseHelper
{
    /** @var string */
    private $baseUrl;

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function generate($route = null, array $params = [])
    {
        return $this->baseUrl . '/' . ltrim(parent::generate($route, $params), '/');
    }
}
