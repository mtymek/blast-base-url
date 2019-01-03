<?php

namespace Blast\BaseUrl;

use Interop\Container\ContainerInterface;

class BasePathTwigExtensionFactory
{
    public function __invoke(ContainerInterface $services)
    {
        if (!method_exists($services, 'configure')) {
            $services = $services->getServiceLocator();
        }
        return new BasePathTwigExtension(
            $services->get(BasePathHelper::class)
        );
    }
}
