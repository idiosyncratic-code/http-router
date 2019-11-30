<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Router;

use ErrorException;
use function debug_backtrace;
use function property_exists;
use function sprintf;

/**
 * @property-read string $handler
 * @property-read array<string, string> $vars
 */
class Route
{
    /**
     * @public-read-only
     * @var string
     */
    private $handler;

    /**
     * @public-read-only
     * @var array<string, string>
     */
    private $vars;

    /**
     * @param array<string, string> $vars
     */
    public function __construct(string $handler, array $vars)
    {
        $this->handler = $handler;

        $this->vars = $vars;
    }

    /**
     * @inheritdoc
     */
    public function __get(string $property)
    {
        if ($this->__isset($property)) {
            return $this->$property;
        }

        $trace = debug_backtrace();

        throw new ErrorException(sprintf(
            'Undefined property: %s in %s on line %s',
            $property,
            $trace[0]['file'],
            $trace[0]['line']
        ));
    }

    /**
     * @inheritdoc
     */
    public function __isset(string $property) : bool
    {
        return property_exists($this, $property);
    }
}
