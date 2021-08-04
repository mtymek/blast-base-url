<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

class BaseUrlMiddlewareFactory
{
    public function __invoke(ContainerInterface $services): BaseUrlMiddleware
    {
        $middleware = new BaseUrlMiddleware();

        if ($services->has(UrlHelper::class)) {
            /** @var UrlHelper $urlHelper */
            $urlHelper = $services->get(UrlHelper::class);
            $middleware->setUrlHelper($urlHelper);
        }

        if ($services->has(BasePathHelper::class)) {
            /** @var BasePathHelper $basePathHelper */
            $basePathHelper = $services->get(BasePathHelper::class);
            $middleware->setBasePathHelper($basePathHelper);
        }

        return $middleware;
    }
}
