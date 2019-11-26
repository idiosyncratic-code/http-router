<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router\RouteCollection;

use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher as FastRouteDispatcher;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use Idiosyncratic\Http\Router\RouteCollection;
use Idiosyncratic\Http\Router\RouteInfo;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use function array_map;
use function class_implements;
use function in_array;
use function is_array;
use function sprintf;
use function strtoupper;

class FastRouteCollection implements RouteCollection
{
    /** @var RouteCollector */
    private $collector;

    /** @var FastRouteDispatcher */
    private $dispatcher;

    public function __construct()
    {
        $this->collector = new RouteCollector(new RouteParser(), new DataGenerator());
    }

    public function matchRoute(ServerRequestInterface $request) : RouteInfo
    {
        $routeInfo = $this->getDispatcher()->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        switch ($routeInfo[0]) {
            case FastRouteDispatcher::METHOD_NOT_ALLOWED:
                throw new RuntimeException(
                    sprintf('Method \'%s\' Not Allowed', strtoupper($request->getMethod()))
                );
                break;
            case FastRouteDispatcher::FOUND:
                return new RouteInfo($routeInfo[1]);
            default:
                throw new RuntimeException('Handler Not Found');
        }
    }

    /**
     * Adds a route to the collection.
     *
     * The syntax used in the $route string depends on the used route parser.
     */
    public function addRoute(string $route, string $handler, string ...$method) : void
    {
        $interfaces = class_implements($handler);

        if (! is_array($interfaces)) {
            throw new RuntimeException(sprintf('Handler %s is not a valid class name', $handler));
        }

        if (in_array(RequestHandlerInterface::class, $interfaces) === false) {
            throw new RuntimeException(sprintf(
                'Handler %s must implement %s',
                $handler,
                ServerRequestInterface::class
            ));
        }

        $this->collector->addRoute(
            array_map(static function ($value) {
                return strtoupper($value);
            }, $method),
            $route,
            $handler
        );
    }

    /**
     * Adds a GET route to the collection
     *
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     */
    public function get(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'GET');
    }

    /**
     * Adds a POST route to the collection
     *
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     */
    public function post(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'POST');
    }

    /**
     * Adds a PUT route to the collection
     *
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     */
    public function put(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'PUT');
    }

    /**
     * Adds a DELETE route to the collection
     *
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     */
    public function delete(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'DELETE');
    }

    /**
     * Adds a PATCH route to the collection
     *
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     */
    public function patch(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'PATCH');
    }

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     */
    public function head(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'HEAD');
    }

    /**
     * Adds an OPTIONS route to the collection
     *
     * This is simply an alias of $this->addRoute('OPTIONS', $route, $handler)
     */
    public function options(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'OPTIONS');
    }

    private function getDispatcher() : FastRouteDispatcher
    {
        if (! $this->dispatcher instanceof FastRouteDispatcher) {
            $this->dispatcher = new GroupCountBasedDispatcher($this->collector->getData());
        }

        return $this->dispatcher;
    }
}
