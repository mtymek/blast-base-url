<?php

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\BaseUrlMiddleware;
use Blast\BaseUrl\UrlHelper;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

class BaseUrlMiddlewareTest extends PHPUnit_Framework_TestCase
{
    public function testMiddlewareUpdatesPathAndSetsAttributes()
    {
        $server = [
            'REQUEST_URI'     => '/index.php/news/3?var1=val1&var2=val2',
            'QUERY_URI'       => 'var1=val1&var2=val2',
            'SCRIPT_NAME'     => '/index.php',
            'PHP_SELF'        => '/index.php/news/3',
            'SCRIPT_FILENAME' => '/var/web/html/index.php',
        ];
        $request = ServerRequestFactory::fromGlobals($server, [], [], [], []);

        $middleware = new BaseUrlMiddleware();
        $middleware($request, new Response(), function (ServerRequestInterface $request) {
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_PATH));
            $this->assertEquals('/index.php', $request->getAttribute(BaseUrlMiddleware::BASE_URL));
            $this->assertEquals('/news/3', $request->getUri()->getPath());
        });
    }

    public function testMiddlewareInjectsUrlHelperWithBaseUrl()
    {
        $server = [
            'REQUEST_URI'     => '/index.php/news/3?var1=val1&var2=val2',
            'QUERY_URI'       => 'var1=val1&var2=val2',
            'SCRIPT_NAME'     => '/index.php',
            'PHP_SELF'        => '/index.php/news/3',
            'SCRIPT_FILENAME' => '/var/web/html/index.php',
        ];
        $request = ServerRequestFactory::fromGlobals($server, [], [], [], []);

        $middleware = new BaseUrlMiddleware();

        $urlHelper = $this->prophesize(UrlHelper::class);
        $urlHelper->setBaseUrl('/index.php')->shouldBeCalled();
        $middleware->setUrlHelper($urlHelper->reveal());

        $middleware($request, new Response(), function (ServerRequestInterface $request) {
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_PATH));
            $this->assertEquals('/index.php', $request->getAttribute(BaseUrlMiddleware::BASE_URL));
            $this->assertEquals('/news/3', $request->getUri()->getPath());
        });
    }
}
