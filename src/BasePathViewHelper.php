<?php

namespace Blast\BaseUrl;

use Zend\View\Helper\AbstractHelper;

class BasePathViewHelper extends AbstractHelper
{
    /** @var BasePathHelper */
    private $basePathHelper;

    /**
     * BasePathViewHelper constructor.
     * @param BasePathHelper $basePathHelper
     */
    public function __construct(BasePathHelper $basePathHelper)
    {
        $this->basePathHelper = $basePathHelper;
    }

    public function __invoke($assetUrl = '')
    {
        $helper = $this->basePathHelper;
        return $helper($assetUrl);
    }
}
