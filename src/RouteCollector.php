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
     * @var \SplObjectStorage|Node[]
     */
    private $leaves;

    public function __construct(){
        $this->tree     = new Node(new Segment(""), null);
        $this->leaves   = new \SplObjectStorage();
    }

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
        if(isset($this->leaves[$route])){
            throw new \InvalidArgumentException();
        }

        if(array_key_exists($route->getName(), $this->routes)){
            throw new \InvalidArgumentException();
        }

        $queue  = new \SplQueue();

        foreach(explode("/", $route->getPath()) as $segment){
            $queue->enqueue(new Segment($segment));
        }

        $queue->dequeue(); // 先頭のセグメント(ここ.com/xxx/xxx)はすでにある(ルートノード)

        $node   = $this->tree;

        while(!$queue->isEmpty()){
            $segment    = $queue->dequeue();

            $node->addChild($segment);

            $node   = $node->getChild($segment);
        }

        $node->addRoute($route);

        $this->routes[$route->getName()]    = $route;
        $this->leaves[$route]               = $node;

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
        if(!array_key_exists($name, $this->routes)){
            return $this;
        }

        $route  = $this->routes[$name];
        $node   = $this->leaves[$route];

        unset($this->routes[$name]);
        unset($this->leaves[$route]);

        $node->removeRoute($route);

        while(0 === count($node->getRoutes())){
            $parent = $node;

            $parent->removeChild($node->getSegment());

            $node   = $parent;
        }

        return $this;
    }
}
