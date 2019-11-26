<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

class RouteInfo
{
    /** @var string */
    private $handler;

    /** @var array<string, string> */
    private $vars;

    public function __construct(string $handler, array $vars)
    {
        $this->handler = $handler;

        $this->vars = $vars;
    }

    public function getHandler() : string
    {
        return $this->handler;
    }

    public function getVars() : array
    {
        return $this->vars;
    }
}
