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
     * @var Parser\Segment
     */
    private $segment;

    /**
     * @var Route
     */
    private $route;

    /**
     *
     * @param   Parser\Segment $segment
     * @param   Node    $parent
     */
    public function __construct(Parser\Segment $segment = null, Node $parent = null){
        if($parent !== null && $segment === null){
            throw new \InvalidArgumentException();
        }
        
        $this->segment  = $segment;
        $this->parent   = $parent ?? $this;
    }

    /**
     *
     *
     * @return  Node
     */
    public function getParent(){
        return $this->parent;
    }

    /**
     *
     *
     * @return  Node[]
     */
    public function getChildren(){
        return $this->children;
    }

    /**
     *
     *
     * @param   Node    $child
     *
     * @return  void
     */
    public function addChild(Node $child){
        $this->children[]   = $child;
    }

    /**
     *
     *
     * @return  Parser\Segment
     */
    public function getSegment(){
        return $this->segment;
    }

    /**
     *
     *
     * @return  Route|null
     */
    public function getRoute(){
        return $this->route;
    }

    /**
     *
     *
     * @param   Route   $route
     *
     * @return  void
     */
    public function setRoute(Route $route){
        $this->route    = $route;
    }

    /**
     *
     *
     * @param   string  $segment
     *
     * @return  bool
     */
    public function isMatch(string $segment){
        return $this->segment->isMatch($segment);
    }
}