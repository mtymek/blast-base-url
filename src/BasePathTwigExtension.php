<?php

namespace Blast\BaseUrl;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BasePathTwigExtension extends AbstractExtension
{
    /** @var BasePathHelper */
    private $basePathHelper;

    /**
     * BasePathTwigExtension constructor.
     * @param BasePathHelper $basePathHelper
     */
    public function __construct(BasePathHelper $basePathHelper)
    {
        $this->basePathHelper = $basePathHelper;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('basePath', [$this, 'render']),
        ];
    }

    public function render(string $assetUrl = '')
    {
        $helper = $this->basePathHelper;
        return $helper($assetUrl);
    }
}
