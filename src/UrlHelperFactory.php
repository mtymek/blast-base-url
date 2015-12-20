<?php

namespace Blast\BasePath;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\Exception\MissingRouterException;
use Zend\Expressive\Router\RouterInterface;

class UrlHelperFactory
{
    /**
     * Create a UrlHelper instance.
     *
     * @param ContainerInterface $container
     * @return UrlHelper
     */
    public function __invoke(ContainerInterface $container)
    {
        if (! $container->has(RouterInterface::class)) {
            throw new MissingRouterException(sprintf(
                '%s requires a %s implementation; none found in container',
                UrlHelper::class,
                RouterInterface::class
            ));
        }

        return new UrlHelper($container->get(RouterInterface::class));
    }
}
