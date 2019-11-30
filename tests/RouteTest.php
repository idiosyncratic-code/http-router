<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use ErrorException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class RouteTest extends TestCase
{
    public function testReadingProperties() : void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);

        $route = new Route(get_class($handler), ['id' => '1']);

        $this->assertEquals(get_class($handler), $route->handler);

        $this->assertEquals('1', $route->vars['id']);
    }

    public function testPropertyDoesNotExist() : void
    {
        $this->expectException(ErrorException::class);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $route = new Route(get_class($handler), ['id' => '1']);

        $route->id;
    }
}
