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

use Fratily\Router\Exception\InvalidSegmentException;
use Fratily\Router\Node\NodeInterface;

/**
 *
 */
interface NodeManagerInterface{

    /**
     * Generate node from segment string.
     *
     * @param string             $segment
     * @param NodeInterface|null $parent
     *
     * @return NodeInterface
     *
     * @throws InvalidSegmentException
     */
    public function generate(string $segment, ?NodeInterface $parent): NodeInterface;
}