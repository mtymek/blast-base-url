<?php

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\UrlHelper;
use PHPUnit_Framework_TestCase;
use Zend\Expressive\Router\RouterInterface;

class UrlHelperTest extends PHPUnit_Framework_TestCase
{
    public function testGeneratePrependsBaseUrl()
    {
        $router = $this->prophesize(RouterInterface::class);
        $router->generateUri('route_name', [])->willReturn('/foo/bar');

        $helper = new UrlHelper($router->reveal());
        $helper->setBaseUrl('/~user/project/public/index.php');
        $url = $helper->generate('route_name', []);
        $this->assertEquals('/~user/project/public/index.php/foo/bar', $url);
    }
}
