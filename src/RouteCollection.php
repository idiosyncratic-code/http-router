<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use Idiosyncratic\Http\Exception\Client\MethodNotAllowed;
use Idiosyncratic\Http\Exception\Client\NotFound;
use Psr\Http\Message\ServerRequestInterface;

interface RouteCollection
{
    /**
     * @throws NotFound
     * @throws MethodNotAllowed
     */
    public function findRoute(ServerRequestInterface $request) : Route;
}
