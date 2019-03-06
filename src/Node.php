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

    private $segment;

    private $methods    = [];

    /**
     * @var Node[]
     */
    private $children   = [];


    /**
     * @var Route
     */
    private $route;

    /**
     *
     * @param   Segment\Segment $segment
     * @param   Node    $parent
     */
    public function __construct(Segment\Segment $segment = null, Node $parent = null){
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
     * @param   Segment\Segment $segment
     *
     * @return  void
     */
    public function addChild(Segment\Segment $segment){
        foreach($this->children as $child){
            if($child->segment === $segment){
                return $child;
            }
        }

        $node               = new Node($segment, $this);
        $this->children[]   = $node;

        return $node;
    }

    /**
     *
     *
     * @return  Segment\Segment
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