<?php

namespace Blast\BaseUrl;

use Interop\Container\ContainerInterface;

class BaseUrlMiddlewareFactory
{
    public function __invoke(ContainerInterface $services)
    {
        $middleware = new BaseUrlMiddleware();

        if ($services->has(UrlHelper::class)) {
            $middleware->setUrlHelper($services->get(UrlHelper::class));
        }

        if ($services->has(BasePathHelper::class)) {
            $middleware->setBasePathHelper($services->get(BasePathHelper::class));
        }

        return $middleware;
    }
}
