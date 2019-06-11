<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface Router
{
    public function dispatch(ServerRequestInterface $request) : ResponseInterface;
}
