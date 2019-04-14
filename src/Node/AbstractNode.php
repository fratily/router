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
use Fratily\Router\Route;

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
     * @var Route|null
     */
    private $route;

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
    public function getName(): string{
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoute(): ?Route{
        return $this->route;
    }

    /**
     * {@inheritDoc}
     */
    public function setRoute(?Route $route): void{
        $this->route    = $route;
    }
}