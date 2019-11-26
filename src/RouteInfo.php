<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use Psr\Http\Server\RequestHandlerInterface;

class RouteInfo
{
    /** @var string|RequestHandlerInterface */
    private $handler;

    /**
     * @param string|RequestHandlerInterface $handler
     */
    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return string|RequestHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
