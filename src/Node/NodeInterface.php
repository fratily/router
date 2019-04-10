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
interface NodeInterface{

    /**
     * Constructor.
     *
     * @param NodeManagerInterface $manager
     * @param NodeInterface        $parent
     */
    public function __construct(NodeManagerInterface $manager, NodeInterface $parent);

    /**
     * Get parent node.
     *
     * If return $this, This node is root of this tree.
     *
     * @return NodeInterface
     */
    public function getParent(): NodeInterface;

    /**
     * Get child nodes.
     *
     * @return NodeInterface[]
     */
    public function getChildren(): array;

    /**
     * Get child node.
     *
     * @param string $segment
     *
     * @return NodeInterface
     */
    public function getChild(string $segment): ?NodeInterface;

    /**
     * Add child node.
     *
     * If already exists same segment node, replace node by $child.
     *
     * @param string        $segment
     * @param NodeInterface $child
     *
     * @return $this
     */
    public function addChild(string $segment): NodeInterface;

    /**
     * Remove child node.
     *
     * @param string $segment
     *
     * @return $this
     */
    public function removeChild(string $segment): NodeInterface;

    /**
     * Get route instance.
     *
     * @return Route|null
     */
    public function getRoute(): ?Route;

    /**
     * Set route instance.
     *
     * @param Route|null $route
     *
     * @return void
     */
    public function setRoute(?Route $route): void;

    /**
     * Check to request segment is match defined segment.
     *
     * $requestSegment must uri decoded.
     *
     * @param string $requestSegment
     *
     * @return bool
     */
    public function isMatch(string $requestSegment): bool;
}