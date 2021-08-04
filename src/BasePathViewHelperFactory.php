<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

use Psr\Container\ContainerInterface;

class BasePathViewHelperFactory
{
    public function __invoke(ContainerInterface $services): BasePathViewHelper
    {
        /** @var BasePathHelper $basePathHelper */
        $basePathHelper = $services->get(BasePathHelper::class);

        return new BasePathViewHelper(
            $basePathHelper
        );
    }
}
