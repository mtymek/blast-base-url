<?php

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\BasePathHelper;
use Blast\BaseUrl\BaseUrlMiddleware;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Expressive\Helper\UrlHelper;

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
        $urlHelper->setBasePath('/index.php')->shouldBeCalled();
        $middleware->setUrlHelper($urlHelper->reveal());

        $middleware($request, new Response(), function (ServerRequestInterface $request) {
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_PATH));
            $this->assertEquals('/index.php', $request->getAttribute(BaseUrlMiddleware::BASE_URL));
            $this->assertEquals('/news/3', $request->getUri()->getPath());
        });
    }

    public function testMiddlewareInjectsBasePathHelperWithBasePath()
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

        $basePathHelper = $this->prophesize(BasePathHelper::class);
        $basePathHelper->setBasePath('/')->shouldBeCalled();
        $middleware->setBasePathHelper($basePathHelper->reveal());

        $middleware($request, new Response(), function (ServerRequestInterface $request) {
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_PATH));
            $this->assertEquals('/index.php', $request->getAttribute(BaseUrlMiddleware::BASE_URL));
            $this->assertEquals('/news/3', $request->getUri()->getPath());
        });
    }

    public function testMiddlewareDoesNotRemoveLeadingSlashWithEmptyBasePath()
    {
        $server = [
            'REQUEST_URI'     => '/news',
            'SCRIPT_NAME'     => '/index.php',
            'PHP_SELF'        => '/news',
            'SCRIPT_FILENAME' => '/var/www/site/public/index.php',
        ];
        $request = ServerRequestFactory::fromGlobals($server, [], [], [], []);

        $middleware = new BaseUrlMiddleware();

        $basePathHelper = $this->prophesize(BasePathHelper::class);
        $basePathHelper->setBasePath('/')->shouldBeCalled();
        $middleware->setBasePathHelper($basePathHelper->reveal());

        $middleware($request, new Response(), function (ServerRequestInterface $request) {
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_PATH));
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_URL));
            $this->assertEquals('/news', $request->getUri()->getPath());
        });
    }
}
