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
            $queue->enqueue($segment);
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

    public function match(string $path, ?string $method, ?string $host): ?Route{
        $stack      = new \SplStack();
        $segments   = explode(
            "/",
            "/" === mb_substr($path, 0, 1) ? mb_substr($path, 1) : $path
        );

        foreach(array_reverse($segments) as $segment){
            $stack->push($segment);
        }

        return $this->recursionMatch($this->tree, $stack, $method, $host);
    }

    private function recursionMatch(Node $node, \SplStack $stack, ?string $method, ?string $host): ?Route{
        $segment    = $stack->pop();
        $param      = $segment;
        $route      = null;

        if($stack->isEmpty() && 0 === count($node->getRoutes())){
            $stack->push($segment);

            return null;
        }

        if(!$this->segmentIsMatch($param, $node->getSegment())){
            $stack->push($segment);

            return null;
        }

        if($stack->isEmpty()){
            foreach($node->getRoutes() as $_route){
                if(
                    (null === $host || fnmatch($_route->getHost(), $host))
                    && (null === $method || in_array($method, $_route->getMethods()))
                ){
                    if(null === $route || $route->getNumber() > $_route->getNumber()){
                        $route  = $_route;
                    }
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

        if(null !== $route && null !== $node->getSegment()->getName()){
            $route  = $route->withParameter($node->getSegment()->getName(), $param);
        }

        $stack->push($segment);

        return $route;
    }

    /**
     * Segment text is matched Segment.
     *
     * @param   string  $segmentText
     * @param   Segment $segment
     *
     * @return  bool
     */
    private function segmentIsMatch(string &$segmentText, Segment $segment): bool{
        $segmentText    = rawurldecode($segmentText);

        if(null !== $segment->getSame() && $segment->getSame() === $segmentText){
            return true;
        }

        if(
            null !== $segment->getRegex()
            && 1 === preg_match("/\A{$segment->getRegex()}\z/", $segmentText, $m)
        ){
            if(null !== $segment->getName() && isset($m[$segment->getName()])){
                $segmentText    = $m[$segment->getName()];
            }

            return true;
        }

        // TODO: この部分の実装が汚いので、今後修正する。
        if("num" === $segment->getFilter()){
            $options    = [
                "options"   => [
                    "min_range" => 0,
                ],
            ];

            $val    = filter_var($segmentText, FILTER_VALIDATE_INT, $options);

            if(false !== $val){
                $segmentText    = $val;

                return true;
            }
        }

        return false;
    }

}
