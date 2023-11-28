<?php

declare(strict_types=1);

namespace Blast\Test\BaseUrl;

use Blast\BaseUrl\BasePathHelper;
use Blast\BaseUrl\BaseUrlMiddleware;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Mezzio\Helper\UrlHelper;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BaseUrlMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    public function testMiddlewareUpdatesPathAndSetsAttributes(): void
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

        $handler = $this->mockHandler(function (ServerRequestInterface $request) {
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_PATH));
            $this->assertEquals('/index.php', $request->getAttribute(BaseUrlMiddleware::BASE_URL));
            $this->assertEquals('/news/3', $request->getUri()->getPath());
        });

        $middleware->process($request, $handler);
    }

    public function testMiddlewareInjectsUrlHelperWithBaseUrl(): void
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

        $handler = $this->mockHandler(function (ServerRequestInterface $request) {
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_PATH));
            $this->assertEquals('/index.php', $request->getAttribute(BaseUrlMiddleware::BASE_URL));
            $this->assertEquals('/news/3', $request->getUri()->getPath());
        });
        $middleware->process($request, $handler);
    }

    public function testMiddlewareInjectsBasePathHelperWithBasePath(): void
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

        $handler = $this->mockHandler(function (ServerRequestInterface $request) {
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_PATH));
            $this->assertEquals('/index.php', $request->getAttribute(BaseUrlMiddleware::BASE_URL));
            $this->assertEquals('/news/3', $request->getUri()->getPath());
        });
        $middleware->process($request, $handler);
    }

    public function testMiddlewareDoesNotRemoveLeadingSlashWithEmptyBasePath(): void
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

        $handler = $this->mockHandler(function (ServerRequestInterface $request) {
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_PATH));
            $this->assertEquals('/', $request->getAttribute(BaseUrlMiddleware::BASE_URL));
            $this->assertEquals('/news', $request->getUri()->getPath());
        });
        $middleware->process($request, $handler);
    }

    private function mockHandler(callable $assertions): RequestHandlerInterface
    {
        return new class ($assertions) implements RequestHandlerInterface {
            /** @var callable */
            private $assertions;

            public function __construct(callable $assertions)
            {
                $this->assertions = $assertions;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                ($this->assertions)($request);
                return new Response();
            }
        };
    }
}
