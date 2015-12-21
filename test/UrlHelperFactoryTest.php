<?php

namespace Blast\BaseUrl;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use Zend\Expressive\Helper\Exception\MissingRouterException;
use Zend\Expressive\Router\RouterInterface;

class UrlHelperFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var ContainerInterface */
    protected $container;

    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $router = $this->prophesize(RouterInterface::class);

        $this->container->get(RouterInterface::class)->willReturn($router);
    }

    public function testFactory()
    {
        $factory = new UrlHelperFactory();
        $this->container->has(RouterInterface::class)->willReturn(true);

        $homePage = $factory($this->container->reveal());

        $this->assertTrue($homePage instanceof UrlHelper);
    }

    public function testFactoryRisesExceptionIfRouterIsNotAvailable()
    {
        $factory = new UrlHelperFactory();
        $this->container->has(RouterInterface::class)->willReturn(false);
        $this->setExpectedException(MissingRouterException::class);

        $factory($this->container->reveal());
    }
}
