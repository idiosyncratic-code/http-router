<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use ErrorException;
use Idiosyncratic\Http\Exception\Client\MethodNotAllowed;
use Idiosyncratic\Http\Exception\Client\NotFound;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class RouteGroupTest extends TestCase
{
    public function testAddRoutes() : void
    {
        $routes = new RouteGroup();

        $uri = $this->createStub(UriInterface::class);

        $uri->method('getPath')
            ->willReturn('/');

        $request = $this->createStub(ServerRequestInterface::class);

        $request->method('getUri')
            ->willReturn($uri);

        $methods = ['get', 'post', 'patch', 'delete', 'put', 'head', 'options'];

        $handlers = [];

        foreach ($methods as $method) {
            $handler = $this->createMock(RequestHandlerInterface::class);
            $handlerClass = get_class($handler);

            $handlers[$method] = $handlerClass;

            call_user_func_array([$routes, $method], ['', $handlerClass]);
        }

        foreach ($methods as $method) {
            $request->method('getMethod')
                ->willReturn(strtoupper($method));

            $route = $routes->findRoute($request);

            $this->assertSame($handlers[$method], $route->handler);
        }
    }

    public function testAddGroup() : void
    {
        $routes = new RouteGroup();

        $uri = $this->createStub(UriInterface::class);

        $uri->method('getPath')
            ->willReturn('/group');

        $request = $this->createStub(ServerRequestInterface::class);

        $request->method('getUri')
            ->willReturn($uri);

        $request->method('getMethod')
            ->willReturn('GET');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handlerClass = get_class($handler);

        $routes->addGroup('group', static function (RouteGroup $routes) use ($handlerClass) {
            $routes->get('', $handlerClass);
        });

        $route = $routes->findRoute($request);

        $this->assertSame($handlerClass, $route->handler);
    }


    public function testNotFound() : void
    {
        $this->expectException(NotFound::class);

        $routes = new RouteGroup();

        $uri = $this->createStub(UriInterface::class);

        $uri->method('getPath')
            ->willReturn('/');

        $request = $this->createStub(ServerRequestInterface::class);

        $request->method('getUri')
            ->willReturn($uri);

        $request->method('getMethod')
            ->willReturn('GET');

        $routes->findRoute($request);
    }

    public function testMethodNotAllowed() : void
    {
        $this->expectException(MethodNotAllowed::class);

        $routes = new RouteGroup();

        $handler = $this->createMock(RequestHandlerInterface::class);

        $routes->get('', get_class($this->createMock(RequestHandlerInterface::class)));

        $uri = $this->createStub(UriInterface::class);

        $uri->method('getPath')
            ->willReturn('/');

        $request = $this->createStub(ServerRequestInterface::class);

        $request->method('getUri')
            ->willReturn($uri);

        $request->method('getMethod')
            ->willReturn('POST');

        $routes->findRoute($request);
    }

    public function testAddingRouteWithNonexistentHandlerClass() : void
    {
        $this->expectException(RuntimeException::class);

        $routes = new RouteGroup();

        $routes->get('', 'handler');
    }

    public function testAddingRouteWithInvalidHandlerClass() : void
    {
        $this->expectException(RuntimeException::class);

        $routes = new RouteGroup();

        $routes->get('', self::class);
    }
}
