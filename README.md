# The Idiosyncratic Router
A lightweight PSR-15 HTTP router implementation.

## Installation
Use [Composer](https://getcomposer.org):

```
composer require idiosyncratic/http-router
```

## Usage
`Idiosyncratic\Http\Router\Router` implementing the PSR-15 `Psr\Http\Server\RequestHandlerInterface` is the main class. It has two dependencies:

- An implementation of `Idiosyncratic\Http\Router\RouteCollection`, a collection of routes implementing a single method:
  ```php
  /**
   * @throws Idiosyncratic\Http\Exception\Client\NotFound
   * @throws Idiosyncratic\Http\Exception\Client\MethodNotAllowed
   */
  public function findRoute(ServerRequestInterface $request) : Idiosyncratic\Http\Router\Route;
  ```
- `Psr\Container\ContainerInterface`, responsible for retrieving the handler for the matched route.



Also included is `Idiosyncratic\Http\Router\RouteGroup`, a basic implementation of the `RouteCollection` interface based on [FastRoute](https://github.com/nikic/FastRoute). The interface for defining routes is nearly identical to [FastRoute's](https://github.com/nikic/FastRoute#defining-routes), with two notable exceptions:

-  The argument order for `RouteGroup::addRoute` is different. Route methods are defined last as string parameters.
- The route handler must be the name of a class implementing `Psr\Http\Server\RequestHandlerInterface`.

Basic usage of the library (using the [PHP League Container](https://container.thephpleague.com)):

```php
$container = new League\Container\Container();

$container->add(ServerRequestInterfaceImplementation::class);

$routes = new Idiosyncratic\Http\Router\RouteGroup();

$routes->addRoute('/hello', ServerRequestInterfaceImplementation::class, 'GET', 'POST');

$router = new Idiosyncratic\Http\Router\Router($routes, $container);

// Create instance of Psr\Http\Message\ServerRequestInterface...

$response = $router->handle($serverRequest);
```
