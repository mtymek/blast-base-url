<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BasePathTwigExtension extends AbstractExtension
{
    private BasePathHelper $basePathHelper;

    /**
     * BasePathTwigExtension constructor
     *
     * @param BasePathHelper $basePathHelper
     */
    public function __construct(BasePathHelper $basePathHelper)
    {
        $this->basePathHelper = $basePathHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('basePath', [$this, 'render']),
        ];
    }

    /**
     * @param string $assetUrl
     *
     * @return string
     */
    public function render(string $assetUrl = ''): string
    {
        $helper = $this->basePathHelper;

        return $helper($assetUrl);
    }
}
