<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

class RouteInfo
{
    /** @var string */
    private $handler;

    public function __construct(string $handler)
    {
        $this->handler = $handler;
    }

    public function getHandler() : string
    {
        return $this->handler;
    }
}
