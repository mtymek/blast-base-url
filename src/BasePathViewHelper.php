<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

use Laminas\View\Helper\AbstractHelper;

class BasePathViewHelper extends AbstractHelper
{
    private BasePathHelper $basePathHelper;

    /**
     * BasePathViewHelper constructor
     *
     * @param BasePathHelper $basePathHelper
     */
    public function __construct(BasePathHelper $basePathHelper)
    {
        $this->basePathHelper = $basePathHelper;
    }

    /**
     * @param string $assetUrl
     *
     * @return string
     */
    public function __invoke(string $assetUrl = ''): string
    {
        $helper = $this->basePathHelper;

        return $helper($assetUrl);
    }
}
