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
namespace Fratily\Router\Node;

use Fratily\Router\NodeManagerInterface;
use Fratily\Router\RouteInterface;

/**
 *
 */
abstract class AbstractNode implements NodeInterface{

    /**
     * @var NodeManagerInterface
     */
    private $manager;

    /**
     * @var NodeInterface
     */
    private $parent;

    /**
     * @var NodeInterface[]
     */
    private $children   = [];

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var RouteInterface[]
     */
    private $routes = [];

    /**
     * {@inheritDoc}
     */
    public function __construct(NodeManagerInterface $manager, ?NodeInterface $parent, ?string $name){
        $this->manager  = $manager;
        $this->parent   = $parent ?? $this;
        $this->name     = $name;
    }

    /**
     * Get node manager.
     *
     * @return NodeManagerInterface
     */
    public function getManager(): NodeManagerInterface{
        return $this->manager;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): NodeInterface{
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren(): array{
        return $this->children;
    }

    /**
     * {@inheritDoc}
     */
    public function getChild(string $segment): ?NodeInterface{
        return $this->children[$segment] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function addChild(string $segment): NodeInterface{
        if(!isset($this->children[$segment])){
            $this->children[$segment]   = $this->getManager()
                ->generate($segment, $this)
            ;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addChildNode(string $segment, NodeInterface $child): NodeInterface{
        if($this !== $child->getParent()){
            throw new \InvalidArgumentException();
        }

        $this->children[$segment]   = $child;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeChild(string $segment): NodeInterface{
        if(array_key_exists($segment, $this->children)){
            unset($this->children[$segment]);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeChildNode(NodeInterface $child): NodeInterface{
        $this->children = array_filter(
            $this->children,
            function($v) use ($child){
                return $child !== $v;
            }
        );

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutes(): array{
        return $this->routes;
    }

    /**
     * {@inheritDoc}
     */
    public function addRoute(RouteInterface $route): void{
        $this->routes[] = $route;
    }

    /**
     * {@inheritDoc}
     */
    public function removeRoute(RouteInterface $route): void{
        $this->routes   = array_values(
            array_diff($this->routes, [$route])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getSegment($parameter): string{
        if(
            is_scalar($parameter)
            || (is_object($parameter) && method_exists($parameter, "__toString"))
        ){
            return (string)$parameter;
        }

        return ":unresolved:";
    }
}
