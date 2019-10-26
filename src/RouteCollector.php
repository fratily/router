<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author     Kento Oka <kento-oka@kentoka.com>
 * @copyright (c) Kento Oka
 * @license   MIT
 * @since     1.0.0
 */
namespace Fratily\Router;

/**
 *
 */
class RouteCollector
{
    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * @var bool
     */
    private $isLocked = false;

    /**
     * Lock the route collector.
     *
     * @return void
     */
    public function lock(): void
    {
        $this->isLocked = true;
    }

    /**
     * Returns whether or not is locked.
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * Returns the routes.
     *
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return array_values($this->routes);
    }

    /**
     * Returns a route by name.
     *
     * @param string $name The route name
     *
     * @return Route
     */
    public function getRoute(string $name): Route
    {
        if (!isset($this->routes[$name])) {
            throw new \LogicException();
        }

        return $this->routes[$name];
    }

    /**
     * Returns whether there are route.
     *
     * @param string $name The route name
     *
     * @return bool
     */
    public function hasRoute(string $name): bool
    {
        return isset($this->routes[$name]);
    }

    /**
     * Add route.
     *
     * @param Route $route The route instance
     *
     * @return $this
     */
    public function addRoute(Route $route): RouteCollector
    {
        if ($this->isLocked()) {
            throw new \LogicException();
        }

        if ($this->hasRoute($route->getName())) {
            throw new \LogicException();
        }

        $this->routes[$route->getName()]    = $route;

        return $this;
    }

    /**
     * Add route instance generated from parameter.
     *
     * @param string      $method The method
     * @param string      $name The name
     * @param string      $path The path
     * @param null        $payload The payload
     *
     * @return $this
     */
    public function addRouteFromParameter(
        string $method,
        string $name,
        string $path,
        $payload = null
    ): RouteCollector {
        if ($this->isLocked()) {
            throw new \LogicException();
        }

        return $this->addRoute(
            (new Route($name, $path, [$method]))->withPayload($payload)
        );
    }

    /**
     * Remove route.
     *
     * @param string $name The route name
     *
     * @return $this
     */
    public function removeRoute(string $name): RouteCollector
    {
        if ($this->isLocked()) {
            throw new \LogicException();
        }

        if (array_key_exists($name, $this->routes)) {
            unset($this->routes[$name]);
        }

        return $this;
    }

    /**
     * Add GET method route.
     *
     * @param string      $name The name
     * @param string      $path The path
     * @param null        $payload The payload
     *
     * @return $this
     */
    public function get(
        string $name,
        string $path,
        $payload = null
    ): RouteCollector {
        if ($this->isLocked()) {
            throw new \LogicException();
        }

        return $this->addRouteFromParameter(Route::GET, $name, $path, $payload);
    }

    /**
     * Add POST method route.
     *
     * @param string      $name The name
     * @param string      $path The path
     * @param null        $payload The payload
     *
     * @return $this
     */
    public function post(
        string $name,
        string $path,
        $payload = null
    ): RouteCollector {
        if ($this->isLocked()) {
            throw new \LogicException();
        }

        return $this->addRouteFromParameter(Route::POST, $name, $path, $payload);
    }

    /**
     * Add PUT method route.
     *
     * @param string      $name The name
     * @param string      $path The path
     * @param null        $payload The payload
     *
     * @return $this
     */
    public function put(
        string $name,
        string $path,
        $payload = null
    ): RouteCollector {
        if ($this->isLocked()) {
            throw new \LogicException();
        }

        return $this->addRouteFromParameter(Route::PUT, $name, $path, $payload);
    }

    /**
     * Add PATCH method route.
     *
     * @param string      $name The name
     * @param string      $path The path
     * @param null        $payload The payload
     *
     * @return $this
     */
    public function patch(
        string $name,
        string $path,
        $payload = null
    ): RouteCollector {
        if ($this->isLocked()) {
            throw new \LogicException();
        }

        return $this->addRouteFromParameter(Route::PATCH, $name, $path, $payload);
    }

    /**
     * Add DELETE method route.
     *
     * @param string      $name The name
     * @param string      $path The path
     * @param null        $payload The payload
     *
     * @return $this
     */
    public function delete(
        string $name,
        string $path,
        $payload = null
    ): RouteCollector {
        if ($this->isLocked()) {
            throw new \LogicException();
        }

        return $this->addRouteFromParameter(Route::DELETE, $name, $path, $payload);
    }
}
