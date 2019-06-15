<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use Psr\Http\Message\ResponseInterface;
use Throwable;

interface ErrorResponseFactory
{
    public function createResponse(
        ServerRequestInterface $request,
        Throwable $exception
    ) : ResponseInterface;
}
