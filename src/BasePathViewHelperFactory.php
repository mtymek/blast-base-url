<?php

namespace Blast\BaseUrl;

use Interop\Container\ContainerInterface;

class BasePathViewHelperFactory
{
    public function __invoke(ContainerInterface $services)
    {
        if (!method_exists($services, 'configure')) {
            $services = $services->getServiceLocator();
        }
        return new BasePathViewHelper(
            $services->get(BasePathHelper::class)
        );
    }
}
