<?php

declare(strict_types=1);

namespace Blast\BaseUrl;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'invokables' => [
                    BasePathHelper::class => BasePathHelper::class,
                ],
            ],
            'view_helpers' => [
                'aliases' => [
                    'basePath' => BasePathHelper::class,
                ],
                'factories' => [
                    BaseUrlMiddleware::class => BaseUrlMiddlewareFactory::class,
                    BasePathHelper::class => BasePathViewHelperFactory::class,
                ],
            ],
        ];
    }
}
