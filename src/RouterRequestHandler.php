<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouterRequestHandler implements RequestHandlerInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var RouteCollection */
    private $routes;

    public function __construct(RouteCollection $routes, ContainerInterface $container)
    {
        $this->routes = $routes;

        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $info = $this->routes->matchRoute($request);

        foreach ($info->getVars() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        $handler = $this->container->get($info->getHandler());

        return $handler->handle($request);
    }
}
