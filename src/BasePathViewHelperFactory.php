<?php

namespace Blast\BaseUrl;

use Interop\Container\ContainerInterface;

class BasePathViewHelperFactory
{
    public function __invoke(ContainerInterface $services)
    {
        $sl = $services->getServiceLocator();
        return new BasePathViewHelper(
            $sl->get(BasePathHelper::class)
        );
    }
}
