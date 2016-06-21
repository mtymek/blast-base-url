<?php

namespace Blast\BaseUrl;

use Interop\Container\ContainerInterface;
use Zend\View\HelperPluginManager;

class BasePathViewHelperFactory
{
    public function __invoke(ContainerInterface $services)
    {
        if ($services instanceof HelperPluginManager) {
            $services = $services->getServiceLocator();
        }

        return new BasePathViewHelper(
            $services->get(BasePathHelper::class)
        );
    }
}
