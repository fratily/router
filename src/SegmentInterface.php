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

/**
 *
 */
interface SegmentInterface
{
    /**
     * Returns the segment name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns whether or not the segment string is match.
     *
     * @param string $segment The segment
     *
     * @return bool
     */
    public function match(string $segment): bool;
}
