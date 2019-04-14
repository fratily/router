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

use Fratily\Router\Node\NodeInterface;
use Fratily\Router\Node\SameNode;

/**
 *
 */
class RouteCollector{

    /**
     * @var NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var SameNode
     */
    private $rootNode;

    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * @var \SplObjectStorage|NodeInterface[]
     */
    private $leaves;

    /**
     * Constructor.
     *
     * @param NodeManagerInterface $nodeManager
     */
    public function __construct(NodeManagerInterface $nodeManager){
        $this->nodeManager  = $nodeManager;
        $this->leaves       = new \SplObjectStorage();
        $this->rootNode     = new SameNode($nodeManager, null, null);

        $this->rootNode->setSame("");
    }

    /**
     * Get node manager.
     *
     * @return NodeManagerInterface
     */
    protected function getNodeManager(): NodeManagerInterface{
        return $this->nodeManager;
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
            throw new \InvalidArgumentException(
                "Route is already registered."
            );
        }

        if(array_key_exists($route->getName(), $this->routes)){
            throw new \InvalidArgumentException(
                "Same name ({$route->getName()}) route is already registered."
            );
        }

        $queue  = new \SplQueue();

        foreach(explode("/", $route->getPath()) as $segment){
            $queue->enqueue($segment);
        }

        $queue->dequeue(); // 先頭のセグメントはすでにある(ルートノード)

        $node   = $this->rootNode;

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

        while(0 === count($node->getRoutes()) && $node !== $node->getParent()){
            $parent = $node->getParent();

            $parent->removeChildNode($node);

            $node   = $parent;
        }

        return $this;
    }

    /**
     * Router.
     *
     * @param string      $path
     * @param string|null $method
     * @param string|null $host
     *
     * @return RouteInterface|null
     */
    public function match(string $path, ?string $method, ?string $host): ?RouteInterface{
        if("/" !== mb_substr($path, 0, 1)){
            $path   = "/" . $path;
        }

        $stack      = new \SplStack();

        foreach(array_reverse(explode("/", $path)) as $segment){
            $stack->push($segment);
        }

        return $this->recursionMatch($this->rootNode, $stack, $method, $host);
    }

    private function recursionMatch(
        NodeInterface $node,
        \SplStack $stack,
        ?string $method,
        ?string $host
    ): ?RouteInterface{
        $segment    = $stack->pop();
        $route      = null;

        if($node->isMatch($segment)){
            if($stack->isEmpty()){
                foreach($node->getRoutes() as $_route){
                    if(
                        (null === $host || fnmatch($_route->getHost(), $host))
                        && (null === $method || in_array($method, $_route->getMethods()))
                    ){
                        $route  = $_route;

                        break;
                    }
                }
            }else{
                foreach($node->getChildren() as $child){
                    $route  = $this->recursionMatch($child, $stack, $method, $host);

                    if(null !== $route){
                        break;
                    }
                }
            }
        }

        $stack->push($segment);

        if(null !== $route && null !== $node->getName()){
            $route->withParameter($node->getName(), $segment);
        }

        return $route;
    }
}
