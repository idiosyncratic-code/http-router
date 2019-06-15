<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class RoutingRequestHandler implements RequestHandlerInterface
{
    /** @var Router */
    private $router;

    /** @var ErrorResponseFactory */
    private $error;

    public function __construct(
        Router $router,
        ErrorResponseFactory $error
    ) {
        $this->router = $router;

        $this->errorFactory = $error;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        try {
            return $this->router->dispatch($request);
        } catch (Throwable $e) {
            return $this->error->createResponse($request, $e);
        }
    }
}
