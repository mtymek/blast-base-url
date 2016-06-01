<?php

namespace Blast\BaseUrl;

use Interop\Container\ContainerInterface;

class BasePathViewHelperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new BasePathViewHelper(
            $container->get(BasePathHelper::class)
        );
    }
}
