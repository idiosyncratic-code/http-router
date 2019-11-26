<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouteCollection
{
    public function matchRoute(ServerRequestInterface $request) : RouteInfo;
}
