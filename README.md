# Fratily Router

`fratily/router` is url routing library.

## Install

``` bash
$ composer require fratily/router
```

## Usage

```php
use Fratily\Router\RouterBuilder;
use Fratily\Router\Route;
use Fratily\Router\RouteOption;

$option = new RouteOption();
$routes = [
    new Route('/', $option->strictCheckTrailing(false)),
    new Route('/foo/bar', $option->strictCheckTrailing(false)),
    new Route('/foo/:name'),
    $matchRoute = (new Route('/foo/:name/setting')),
    new Route('/foo/:name/profile'),
    new Route('/bar'),
    new Route('/baz'),
];

$router = (new RouterBuilder($routes))->build();

[
    'route' => $route, // $matchRoute
    'params' => $params // ['name' => 'any']
] = $router->match('/foo/any/setting');
```
