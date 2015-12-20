<?php

namespace Blast\BasePath;

use Interop\Container\ContainerInterface;

class BasePathMiddlewareFactory
{
    public function __invoke(ContainerInterface $services)
    {
        $middleware = new BasePathMiddleware();

        if ($services->has(UrlHelper::class)) {
            $middleware->setUrlHelper($services->get(UrlHelper::class));
        }

        return $middleware;
    }
}
