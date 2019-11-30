<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\Dispatcher as FastRouteDispatcher;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use Idiosyncratic\Http\Exception\Client\MethodNotAllowed;
use Idiosyncratic\Http\Exception\Client\NotFound;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use function array_map;
use function class_implements;
use function in_array;
use function is_array;
use function sprintf;
use function strtoupper;

final class RouteGroup implements RouteCollection
{
    /** @var RouteCollector */
    private $collector;

    /** @var FastRouteDispatcher */
    private $dispatcher;

    /** @var string */
    private $prefix;

    public function __construct(string $prefix = '/')
    {
        $this->prefix = $prefix;

        $this->collector = new RouteCollector(new RouteParser(), new DataGenerator());
    }

    /**
     * @inheritdoc
     */
    public function findRoute(ServerRequestInterface $request) : Route
    {
        $routeInfo = $this->getDispatcher()->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        switch ($routeInfo[0]) {
            case FastRouteDispatcher::FOUND:
                return new Route($routeInfo[1], $routeInfo[2]);
            case FastRouteDispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowed($request);
            default:
                throw new NotFound($request);
        }
    }

    /**
     * Adds a route to the collection.
     */
    public function addRoute(string $route, string $handler, string ...$method) : void
    {
        if (class_exists($handler) === false) {
            throw new RuntimeException(sprintf('Handler %s is not a valid class name', $handler));
        }

        if (in_array(RequestHandlerInterface::class, class_implements($handler)) === false) {
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
            $this->prefix . $route,
            $handler
        );
    }

    public function addGroup(string $prefix, callable $callback) : void
    {
        $prefixBackup = $this->prefix;

        $this->prefix .= $prefix;

        $callback($this);

        $this->prefix = $prefixBackup;
    }

    /**
     * Adds a GET route to the collection
     */
    public function get(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'GET');
    }

    /**
     * Adds a POST route to the collection
     */
    public function post(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'POST');
    }

    /**
     * Adds a PUT route to the collection
     */
    public function put(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'PUT');
    }

    /**
     * Adds a DELETE route to the collection
     */
    public function delete(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'DELETE');
    }

    /**
     * Adds a PATCH route to the collection
     */
    public function patch(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'PATCH');
    }

    /**
     * Adds a HEAD route to the collection
     */
    public function head(string $route, string $handler) : void
    {
        $this->addRoute($route, $handler, 'HEAD');
    }

    /**
     * Adds an OPTIONS route to the collection
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
