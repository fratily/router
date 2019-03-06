<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento-oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Router;

/**
 *
 */
class RouteCollector{

    /**
     * @var Node
     */
    private $tree;

    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * Get all routes.
     *
     * @return  Route[]
     */
    public function all(): array{
        $this->routes;
    }

    /**
     * Get route.
     *
     * @param   string  $name
     *
     * @return  Route|null
     */
    public function get(string $name): ?Route{
        return $this->routes[$name] ?? null;
    }

    /**
     * Add route.
     *
     * @param   Route   $route
     *
     * @return  $this
     */
    public function add(Route $route): self{

        if(array_key_exists($route->getName(), $this->routes)){
            throw new \InvalidArgumentException();
        }

        $this->routes[$route->getName()]    = $route;

        return $this;
    }

    /**
     * Remove route.
     *
     * @param   string  $name
     *
     * @return  $this
     */
    public function remove(string $name): self{

    }
}
