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

/**
 *
 */
class AnyNode extends AbstractNode{

    /**
     * {@inheritDoc}
     */
    public function isMatch(string $requestSegment): bool{
        return "" !== $requestSegment;
    }
}