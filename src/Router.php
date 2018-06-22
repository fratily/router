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
class Router{

    /**
     * @var Parser\ParserInterface
     */
    private $parser;

    /**
     * @var Node
     */
    private $tree;

    /**
     * @var Routing[]
     */
    private $cache  = [];

    /**
     * Constructor
     *
     * @param   array[] $routes
     */
    public function __construct(Parser\ParserInterface $parser, array $routes){
        $this->parser   = $parser;
        $this->tree     = new Node();

        foreach($routes as $route){
            $this->addRoute($route);
        }
    }

    /**
     * ルートを追加する
     *
     * @param   Route   $route
     *
     * @return  void
     */
    protected function addRoute(Route $route){
        $segments   = $route->getSegments();
        $parent     = $this->tree;

        foreach($segments as $segment){
            $node   = new Node($segment, $parent);

            $parent->addChild($node);

            $parent = $node;
        }

        $parent->setRoute($route);
    }

    /**
     * 一致するルートを探す
     *
     * @param   string  $path
     *
     * @return  mixed[]
     */
    public function search(string $path){
        if(!array_key_exists($path, $this->cache)){
            $search = $this->searchNode(
                array_reverse($this->parser->split2Segments($path)),
                $this->tree->getChildren()
            );

            $this->cache[$path] = $search === false
                ? new Routing()
                : new Routing($search[0], $search[1])
            ;
        }

        return $this->cache[$path];
    }

    /**
     * ルーティングツリーのノードを探索する
     *
     * @param   string[]    $segments
     * @param   Node[]  $nodes
     * @param   mixed[] $params
     *
     * @return  mixed[]|bool
     */
    private function searchNode(array $segments, array $nodes, array $params = []){
        $segment    = array_pop($segments);

        foreach($nodes as $node){
            $_param = [];
            $match  = $node->isMatch($segment);

            if($match){
                if($node->getSegment()->getName() !== null){
                    $_param[$node->getSegment()->getName()] = $node->getSegment()->convert($segment);
                }

                if(empty($segments)){
                    if($node->getRoute() instanceof Route){
                        return [
                            $node->getRoute(),
                            array_merge($params, $_param),
                        ];
                    }

                    continue;
                }

                $result = $this->searchNode(
                    $segments,
                    $node->getChildren(),
                    array_merge($params, $_param)
                );

                if($result !== false){
                    return $result;
                }
            }
        }

        return false;
    }
}