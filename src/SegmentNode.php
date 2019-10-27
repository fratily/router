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
class SegmentNode
{
    /**
     * @var SegmentNode|null
     */
    private $parent;

    /**
     * @var string
     */
    private $defaultValue;

    /**
     * @var SegmentNode[]
     */
    private $children = [];

    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * @var \SplObjectStorage|string[][]
     */
    private $parameterNameMapsByRoute;

    /**
     * Constructor.
     *
     * @param SegmentNode|null $parent The parent node
     * @param string           $defaultValue The default segment value
     */
    public function __construct(?SegmentNode $parent, string $defaultValue = "")
    {
        $this->parent = $parent;
        $this->defaultValue = $defaultValue;
    }

    /**
     * Returns the parent node.
     *
     * @return SegmentNode|null
     */
    public function getParent(): ?SegmentNode
    {
        return $this->parent;
    }

    /**
     * Returns the default segment value.
     *
     * @return string
     */
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * Returns the child segment names.
     *
     * @return string[]
     */
    public function getChildSegmentNames(): array
    {
        return array_keys($this->children);
    }

    /**
     * Returns the child node by segment name.
     *
     * @param string $segment The segment name
     *
     * @return SegmentNode
     */
    public function getChild(string $segment): SegmentNode
    {
        if (!$this->hasChild($segment)) {
            throw new \LogicException();
        }

        return $this->children[$segment];
    }

    /**
     * Returns whether the child node has a segment.
     *
     * @param string $segment The segment node
     *
     * @return bool
     */
    public function hasChild(string $segment): bool
    {
        return isset($this->children[$segment]);
    }

    /**
     * Add the child node by segment name.
     *
     * @param string $segment The segment node
     * @param string $defaultValue The default segment value
     *
     * @return SegmentNode
     */
    public function addChild(string $segment, string $defaultValue = ""): SegmentNode
    {
        if (!$this->hasChild($segment)) {
            $this->children[$segment] = new SegmentNode($this, $defaultValue);
        }

        return $this->children[$segment];
    }

    /**
     * Returns the route by method.
     *
     * @param string $method The method
     *
     * @return Route
     */
    public function getRoute(string $method): Route
    {
        if (!$this->hasRoute($method)) {
            throw new \LogicException();
        }

        return $this->routes[$method];
    }

    /**
     * Returns the route parameter name map by method.
     *
     * @param Route $route The route
     *
     * @return string[]
     */
    public function getParameterNameMap(Route $route): array
    {
        if (!isset($this->parameterNameMapsByRoute[$route])) {
            throw new \LogicException();
        }

        return $this->parameterNameMapsByRoute[$route];
    }

    /**
     * Returns whether there is a route.
     *
     * @param string $method The method
     *
     * @return bool
     */
    public function hasRoute(string $method): bool
    {
        return isset($this->routes[$method]);
    }

    /**
     * Add the route.
     *
     * @param Route    $route The route
     * @param string[] $parameterNameMap The parameter name map
     *
     * @return SegmentNode
     */
    public function addRoute(Route $route, array $parameterNameMap = []): SegmentNode
    {
        $methods = $route->getMethods();

        foreach ($methods as $method) {
            if ($this->hasRoute($method)) {
                throw new \LogicException();
            }
        }

        foreach ($methods as $method) {
            $this->routes[$method] = $route;
        }

        if (null === $this->parameterNameMapsByRoute) {
            $this->parameterNameMapsByRoute = new \SplObjectStorage();
        }

        $this->parameterNameMapsByRoute[$route] = $parameterNameMap;

        return $this;
    }
}
