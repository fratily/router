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

use Fratily\Router\Segments\SameSegment;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException as SimpleCacheInvalidArgumentException;

/**
 *
 */
class Router
{
    /**
     * @var RouteCollector
     */
    private $routeCollector;

    /**
     * @var SegmentManager
     */
    private $segmentManager;

    /**
     * @var CacheInterface|null
     */
    private $cache;

    /**
     * @var int|null
     */
    private $cacheTtl;

    /**
     * @var SegmentNode
     */
    private $node;

    /**
     * @var \SplObjectStorage|SegmentNode[]
     */
    private $nodesByRoute;

    /**
     * Constructor.
     *
     * @param RouteCollector      $routeCollector The route collector
     * @param SegmentManager      $segmentManager The segment manager
     * @param CacheInterface|null $cache The cache instance
     * @param int|null            $cacheTtl The cache ttl
     *
     * @throws SimpleCacheInvalidArgumentException
     */
    public function __construct(
        RouteCollector $routeCollector,
        SegmentManager $segmentManager,
        CacheInterface $cache = null,
        int $cacheTtl = null
    ) {
        $this->routeCollector = $routeCollector;
        $this->segmentManager = $segmentManager;
        $this->cache = $cache;
        $this->cacheTtl = $cacheTtl;

        $this->routeCollector->lock();

        [$this->node, $this->nodesByRoute] = $this->generateInitializedNode();

        if (!is_object($this->node) || !$this->node instanceof SegmentNode) {
            throw new \UnexpectedValueException();
        }

        if (
            !is_object($this->nodesByRoute)
            || !$this->nodesByRoute instanceof \SplObjectStorage
        ) {
            throw new \UnexpectedValueException();
        }

        $this->segmentManager->lock();
    }

    /**
     * Returns the list of route tree and nodes by route array.
     *
     * @return array
     *
     * @throws SimpleCacheInvalidArgumentException
     */
    protected function generateInitializedNode(): array
    {
        if (
            null !== $this->getCache()
            && $this->getCache()->has($this->getCacheKey())
        ) {
            return $this->getCache()->get($this->getCacheKey());
        }

        $root = new SegmentNode(null);
        $nodesByRoute = new \SplObjectStorage();
        $sameSegmentNames = [];

        foreach ($this->getRouteCollector()->getRoutes() as $route) {
            $node = $root;
            $parameterNameMap = [];
            $index = 0;
            $segments = explode(
                "/",
                "/" === substr($route->getPath(), 0, 1)
                    ? substr($route->getPath(), 1)
                    : $route->getPath()
            );

            foreach ($segments as $segment) {
                $firstChar = substr($segment, 0, 1);
                $parameterName = null;
                $segmentName = null;

                if (":" === $firstChar || "@" === $firstChar) {
                    $segment = substr($segment, 1);

                    if (":" === $firstChar) {
                        [$parameterName, $segment] = array_pad(
                            explode("@", $segment, 2),
                            2,
                            null
                        );

                        if ("" === $parameterName) {
                            throw new \LogicException();
                        }
                    }

                    if ("" === $segment) {
                        throw new \LogicException();
                    }

                    $segmentName = $segment ?? $this->getSegmentManager()->getDefaultSegmentName();

                    if (null === $segmentName) {
                        throw new \LogicException();
                    }
                } else {
                    if (!isset($sameSegmentNames[$segment])) {
                        $segmentName = SameSegment::instance($segment)->getName();

                        if (!$this->getSegmentManager()->hasSegment($segmentName)) {
                            $this->getSegmentManager()->addSegment(
                                SameSegment::instance($segment)
                            );
                        }

                        $sameSegmentNames[$segment] = $segmentName;
                    }

                    $segmentName = $sameSegmentNames[$segment];
                }

                $parameterNameMap[$index++] = $parameterName;
                $node = $node->addChild($segmentName);
            }

            $node->addRoute($route, $parameterNameMap);

            $nodesByRoute[$route] = $node;
        }

        return [$root, $nodesByRoute];
    }

    /**
     * Returns the route collector.
     *
     * @return RouteCollector
     */
    protected function getRouteCollector(): RouteCollector
    {
        return $this->routeCollector;
    }

    /**
     * Returns the segment manager.
     *
     * @return SegmentManager
     */
    protected function getSegmentManager(): SegmentManager
    {
        return $this->segmentManager;
    }

    /**
     * Returns the cache manager.
     *
     * @return CacheInterface|null
     */
    protected function getCache(): ?CacheInterface
    {
        return $this->cache;
    }

    /**
     * Returns the cache key.
     *
     * @return string
     */
    protected function getCacheKey(): string
    {
        return "fratily.router.node";
    }

    /**
     * Returns the root segment node.
     *
     * @return SegmentNode
     */
    protected function getNode(): SegmentNode
    {
        return $this->node;
    }

    /**
     * Returns the leaf node by route.
     *
     * @param Route $route
     *
     * @return SegmentNode|null
     */
    protected function getLeafNodeByRoute(Route $route): ?SegmentNode
    {
        return $this->nodesByRoute[$route] ?? null;
    }

    /**
     * Returns the route that matched the request.
     *
     * @param string $method The request http method
     * @param string $path The request path
     *
     * @return MatchedRoute|null
     */
    public function match(string $method, string $path): ?MatchedRoute
    {
        $segmentStack = new \SplStack();
        $segments = explode(
            "/",
            "/" === substr($path, 0, 1) ? substr($path, 1) : $path
        );

        foreach ($segments as $segment) {
            $segmentStack->unshift($segment);
        }

        return $this->recursiveMatch(
            $segments,
            $segmentStack,
            $this->getNode(),
            $method
        );
    }

    /**
     * Recursive search of the tree and return the matched root.
     *
     * @param string[]    $segments The segments
     * @param \SplStack   $segmentStack The segments stack
     * @param SegmentNode $node The segment node
     * @param string      $method The request http method
     *
     * @return MatchedRoute|null
     */
    private function recursiveMatch(
        array $segments,
        \SplStack $segmentStack,
        SegmentNode $node,
        string $method
    ): ?MatchedRoute {
        static $i = 0;
        $i++;

        if ($segmentStack->isEmpty()) {
            if (!$node->hasRoute($method)) {
                return null;
            }

            $route = $node->getRoute($method);

            $parameters = [];

            foreach ($node->getParameterNameMap($route) as $index => $name) {
                if (!isset($segments[$index])) {
                    throw new \LogicException();
                }

                if (null !== $name) {
                    $parameters[$name] = $segments[$index];
                }
            }

            return new MatchedRoute(
                $route->getName(),
                $parameters,
                $route->getPayload()
            );
        }

        $segment = $segmentStack->pop();

        if (!is_string($segment)) {
            throw new \LogicException();
        }

        $matchSegmentNames = $this->getSegmentManager()->getMatchSegmentNames(
            $segment,
            $node->getChildSegmentNames()
        );

        foreach ($matchSegmentNames as $matchSegmentName) {
            $matchedRoute = $this->recursiveMatch(
                $segments,
                $segmentStack,
                $node->getChild($matchSegmentName),
                $method
            );

            if (null !== $matchedRoute) {
                return $matchedRoute;
            }
        }

        return null;
    }
}
