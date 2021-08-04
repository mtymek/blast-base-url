<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

use Psr\Container\ContainerInterface;

class BasePathTwigExtensionFactory
{
    public function __invoke(ContainerInterface $services): BasePathTwigExtension
    {
        /** @var BasePathHelper $basePathHelper */
        $basePathHelper = $services->get(BasePathHelper::class);

        return new BasePathTwigExtension(
            $basePathHelper
        );
    }
}
