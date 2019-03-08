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
class Node{

    /**
     * @var Node
     */
    private $parent;

    /**
     * @var Node[]
     */
    private $children   = [];

    /**
     * @var Segment
     */
    private $segment;

    /**
     * @var \SplObjectStorage|Route[]
     */
    private $routes = [];

    /**
     * Constructor.
     *
     * @param   Segment $segment
     * @param   Node|null   $parent
     *
     */
    public function __construct(Segment $segment, ?Node $parent){
        $this->segment  = $segment;
        $this->parent   = $parent ?? $this;
        $this->routes   = new \SplObjectStorage();
    }

    /**
     * Is root.
     *
     * @return  bool
     */
    public function isRoot(): bool{
        return $this->parent === $this;
    }

    /**
     * Get parent node.
     *
     * @return  Node
     */
    public function getParent(): Node{
        return $this->parent;
    }

    /**
     * Get child nodes.
     *
     * @return  Node[]
     */
    public function getChildren(): array{
        return $this->children;
    }

    /**
     * Get child.
     *
     * @param   string  $segment
     *
     * @return  Node|null
     */
    public function getChild(string $segment): ?Node{
        return $this->children[$segment] ?? null;
    }

    /**
     * Add child.
     *
     * @param   string  $segment
     *
     * @return  $this
     */
    public function addChild(string $segment): self{
        if(!array_key_exists($segment, $this->getChildren())){
            $this->children[$segment]  = new Node(new Segment($segment), $this);
        }

        return $this;
    }

    /**
     * Remove child.
     *
     * @param   string  $segment
     *
     * @return  $this
     */
    public function removeChild(string $segment): self{
        if(array_key_exists($segment, $this->getChildren())){
            unset($this->children[$segment]);
        }

        return $this;
    }

    /**
     * Get segment.
     *
     * @return  Segment
     */
    public function getSegment(): Segment{
        return $this->segment;
    }

    /**
     * Get routes.
     *
     * @return  Route[]
     */
    public function getRoutes(): iterable{
        yield from $this->routes;
    }

    /**
     * Add route.
     *
     * @param   Route   $route
     *
     * @return  $this
     */
    public function addRoute(Route $route): self{
        if(array_key_exists($route->getName(), $this->routes)){
            return $this;
        }

        $this->routes[$route->getName()]    = $route;

        return $this;
    }

    /**
     * Remove route.
     *
     * @param   Route   $route
     *
     * @return  $this
     */
    public function removeRoute(Route $route): self{
        if(array_key_exists($route->getName(), $this->routes)){
            unset($this->routes[$route->getName()]);
        }

        return $this;
    }
}