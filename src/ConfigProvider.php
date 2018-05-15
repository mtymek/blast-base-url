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
                    BaseUrlMiddleware::class => BaseUrlMiddleware::class,
                ],
            ],
            'view_helpers' => [
                'aliases' => [
                    'basePath' => BasePathHelper::class,
                ],
                'factories' => [
                    BasePathHelper::class => BasePathViewHelperFactory::class,
                ],
            ],
        ];
    }
}
