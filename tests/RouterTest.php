<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use ErrorException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouterTest extends TestCase
{
    public function testHandlesRequests() : void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($expectedResponse);
        $handlerClass = get_class($handler);

        $container = $this->createStub(ContainerInterface::class);
        $container->method('get')
             ->will($this->returnValueMap([[$handlerClass, $handler]]));

        $routes = $this->createStub(RouteCollection::class);

        $routes->method('findRoute')
            ->willReturn(
                new Route($handlerClass, ['id' => '1'])
            );

        $router = new Router($routes, $container);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('withAttribute')
            ->will($this->returnSelf());

        $actualResponse = $router->handle($request);

        $this->assertSame($expectedResponse, $actualResponse);
    }
}
