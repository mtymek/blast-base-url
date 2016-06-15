<?php

namespace Blast\BaseUrl;

use Interop\Container\ContainerInterface;

class BasePathViewHelperFactory
{
    public function __invoke(ContainerInterface $services)
    {
        return new BasePathViewHelper(
            $services->get(BasePathHelper::class)
        );
    }
}
